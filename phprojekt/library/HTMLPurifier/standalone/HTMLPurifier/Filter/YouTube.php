<?php

class HTMLPurifier_Filter_YouTube extends HTMLPurifier_Filter
{
    
    public $name = 'YouTube';
    
    public function preFilter($html, $config, $context) {
        $pre_regex = '#<object[^>]+>.+?'.
            'http://www.youtube.com/v/([A-Za-z0-9\-_]+).+?</object>#s';
        $pre_replace = '<span class="youtube-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }
    
    /**
     * Replaces YouTube embed codes with HTML object tags.
     *
     * This method takes an HTML string, searches for any <span> tags with the class 'youtube-embed', and replaces them with an HTML <object> tag that embeds the corresponding YouTube video.
     * This allows the HTML Purifier to safely render YouTube video embeds without introducing potential security vulnerabilities.
     *
     * @param string $html The HTML content to be processed
     * @param HTMLPurifier_Config $config The HTML Purifier configuration object
     * @param HTMLPurifier_Context $context The HTML Purifier context object
     * @return string The HTML content with YouTube embeds replaced by <object> tags
     */
    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="youtube-embed">([A-Za-z0-9\-_]+)</span>#';
        $post_replace = '<object width="425" height="350" '.
            'data="http://www.youtube.com/v/\1">'.
            '<param name="movie" value="http://www.youtube.com/v/\1"></param>'.
            '<param name="wmode" value="transparent"></param>'.
            '<!--[if IE]>'.
            '<embed src="http://www.youtube.com/v/\1"'.
            'type="application/x-shockwave-flash"'.
            'wmode="transparent" width="425" height="350" />'.
            '<![endif]-->'.
            '</object>';
        return preg_replace($post_regex, $post_replace, $html);
    }
    
}

