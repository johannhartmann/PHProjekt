<?php

/**
 * This filter extracts <style> blocks from input HTML, cleans them up
 * using CSSTidy, and then places them in $purifier->context->get('StyleBlocks')
 * so they can be used elsewhere in the document.
 * 
 * @note
 *      See tests/HTMLPurifier/Filter/ExtractStyleBlocksTest.php for
 *      sample usage.
 * 
 * @note
 *      This filter can also be used on stylesheets not included in the
 *      document--something purists would probably prefer. Just directly
 *      call HTMLPurifier_Filter_ExtractStyleBlocks->cleanCSS()
 */
class HTMLPurifier_Filter_ExtractStyleBlocks extends HTMLPurifier_Filter
{
    
    public $name = 'ExtractStyleBlocks';
    private $_styleMatches = array();
    private $_tidy;
    
    public function __construct() {
        $this->_tidy = new csstidy();
    }
    
    /**
     * Save the contents of CSS blocks to style matches
     * @param $matches preg_replace style $matches array
     */
    protected function styleCallback($matches) {
        $this->_styleMatches[] = $matches[1];
    }
    
    /**
     * Extracts inline <style> blocks from HTML and optionally cleans the CSS content.
     *
     * This method is responsible for finding all inline <style> blocks in the provided HTML, extracting their contents, and storing them in the provided context object.
     * Optionally, it can also clean the CSS content of the extracted style blocks using the cleanCSS() method.
     *
     * @param string $html The HTML content to process
     * @param HTMLPurifier_Config $config The HTMLPurifier configuration object
     * @param HTMLPurifier_Context $context The HTMLPurifier context object
     * @return string The original HTML with the inline <style> blocks removed
     * @todo Extend to indicate non-text/css style blocks
     * @see HTMLPurifier_Filter_ExtractStyleBlocks::cleanCSS
     */
    public function preFilter($html, $config, $context) {
        $tidy = $config->get('FilterParam', 'ExtractStyleBlocksTidyImpl');
        if ($tidy !== null) $this->_tidy = $tidy;
        $html = preg_replace_callback('#<style(?:\s.*)?>(.+)</style>#isU', array($this, 'styleCallback'), $html);
        $style_blocks = $this->_styleMatches;
        $this->_styleMatches = array(); // reset
        $context->register('StyleBlocks', $style_blocks); // $context must not be reused
        if ($this->_tidy) {
            foreach ($style_blocks as &$style) {
                $style = $this->cleanCSS($style, $config, $context);
            }
        }
        return $html;
    }
    
    /**
     * Cleans and sanitizes CSS code by removing comments, validating properties, and escaping special characters.
     *
     * This method takes CSS code as input, removes any HTML comments, validates the CSS properties using the provided configuration and context, and returns the cleaned CSS code.
     * It also removes certain CSS constructs like @import, @charset, and @namespace that could potentially introduce security risks.
     *
     * @warning Requires CSSTidy <http://csstidy.sourceforge.net/>
     * @param string $css The CSS code to be cleaned and sanitized
     * @param HTMLPurifier_Config $config An instance of the HTMLPurifier_Config class, which provides configuration options for the cleaning process
     * @param HTMLPurifier_Context $context An instance of the HTMLPurifier_Context class, which provides contextual information for the cleaning process
     * @return string The cleaned and sanitized CSS code
     */
    public function cleanCSS($css, $config, $context) {
        // prepare scope
        $scope = $config->get('FilterParam', 'ExtractStyleBlocksScope');
        if ($scope !== null) {
            $scopes = array_map('trim', explode(',', $scope));
        } else {
            $scopes = array();
        }
        // remove comments from CSS
        $css = trim($css);
        if (strncmp('<!--', $css, 4) === 0) {
            $css = substr($css, 4);
        }
        if (strlen($css) > 3 && substr($css, -3) == '-->') {
            $css = substr($css, 0, -3);
        }
        $css = trim($css);
        $this->_tidy->parse($css);
        $css_definition = $config->getDefinition('CSS');
        foreach ($this->_tidy->css as $k => $decls) {
            // $decls are all CSS declarations inside an @ selector
            $new_decls = array();
            foreach ($decls as $selector => $style) {
                $selector = trim($selector);
                if ($selector === '') continue; // should not happen
                if ($selector[0] === '+') {
                    if ($selector !== '' && $selector[0] === '+') continue;
                }
                if (!empty($scopes)) {
                    $new_selector = array(); // because multiple ones are possible
                    $selectors = array_map('trim', explode(',', $selector));
                    foreach ($scopes as $s1) {
                        foreach ($selectors as $s2) {
                            $new_selector[] = "$s1 $s2";
                        }
                    }
                    $selector = implode(', ', $new_selector); // now it's a string
                }
                foreach ($style as $name => $value) {
                    if (!isset($css_definition->info[$name])) {
                        unset($style[$name]);
                        continue;
                    }
                    $def = $css_definition->info[$name];
                    $ret = $def->validate($value, $config, $context);
                    if ($ret === false) unset($style[$name]);
                    else $style[$name] = $ret;
                }
                $new_decls[$selector] = $style;
            }
            $this->_tidy->css[$k] = $new_decls;
        }
        // remove stuff that shouldn't be used, could be reenabled
        // after security risks are analyzed
        $this->_tidy->import = array();
        $this->_tidy->charset = null;
        $this->_tidy->namespace = null;
        $css = $this->_tidy->print->plain();
        // we are going to escape any special characters <>& to ensure
        // that no funny business occurs (i.e. </style> in a font-family prop).
        if ($config->get('FilterParam', 'ExtractStyleBlocksEscaping')) {
            $css = str_replace(
                array('<',    '>',    '&'),
                array('\3C ', '\3E ', '\26 '),
                $css
            );
        }
        return $css;
    }
    
}

