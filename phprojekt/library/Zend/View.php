<?php
/**
 * Compatibility shim for Zend_View
 */

use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplatePathStack;

class Zend_View extends PhpRenderer
{
    /**
     * Script paths
     * @var array
     */
    protected $_scriptPaths = [];

    /**
     * View variables
     * @var array
     */
    protected $_vars = [];

    /**
     * Constructor
     */
    public function __construct($config = [])
    {
        parent::__construct();

        // Set up path resolver
        $resolver = new TemplatePathStack();
        $this->setResolver($resolver);

        if (isset($config['scriptPath'])) {
            $this->setScriptPath($config['scriptPath']);
        }
    }

    /**
     * Add script path
     */
    public function addScriptPath($path)
    {
        if (!in_array($path, $this->_scriptPaths)) {
            $this->_scriptPaths[] = $path;
            $resolver = $this->resolver();
            if ($resolver instanceof TemplatePathStack) {
                $resolver->addPath($path);
            }
        }
        return $this;
    }

    /**
     * Set script path
     */
    public function setScriptPath($path)
    {
        $this->_scriptPaths = [];
        if (is_array($path)) {
            foreach ($path as $p) {
                $this->addScriptPath($p);
            }
        } else {
            $this->addScriptPath($path);
        }
        return $this;
    }

    /**
     * Get script paths
     */
    public function getScriptPaths()
    {
        return $this->_scriptPaths;
    }

    /**
     * Assign variable
     */
    public function assign($spec, $value = null)
    {
        if (is_array($spec)) {
            foreach ($spec as $key => $val) {
                $this->_vars[$key] = $val;
                $this->$key = $val;
            }
        } else {
            $this->_vars[$spec] = $value;
            $this->$spec = $value;
        }
        return $this;
    }

    /**
     * Clear all assigned variables
     */
    public function clearVars()
    {
        $this->_vars = [];
        return $this;
    }

    /**
     * Get all assigned variables
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * Render a template
     */
    public function render($nameOrModel, $values = null)
    {
        // Transfer all variables to the view
        foreach ($this->_vars as $key => $value) {
            $this->$key = $value;
        }

        // Handle values parameter if provided
        if ($values !== null && is_array($values)) {
            foreach ($values as $key => $value) {
                $this->$key = $value;
            }
        }

        return parent::render($nameOrModel, $values);
    }

    /**
     * Escape output
     */
    public function escape($var)
    {
        return $this->escapeHtml($var);
    }

    /**
     * Add helper path (for compatibility)
     */
    public function addHelperPath($path, $prefix)
    {
        // Laminas uses plugin managers differently
        // This is a stub for compatibility
        return $this;
    }

    /**
     * Set base path
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        $this->addScriptPath($path . '/scripts');
        return $this;
    }

    /**
     * Get helper (for compatibility)
     */
    public function getHelper($name)
    {
        // Return a simple helper proxy
        return new Zend_View_Helper_Proxy($name, $this);
    }
}

/**
 * Helper proxy for compatibility
 */
class Zend_View_Helper_Proxy
{
    protected $_name;
    protected $_view;

    public function __construct($name, $view)
    {
        $this->_name = $name;
        $this->_view = $view;
    }

    public function __call($method, $args)
    {
        // Try to call the helper through Laminas plugin system
        try {
            if ($this->_view->plugin($this->_name)) {
                return call_user_func_array([$this->_view->plugin($this->_name), $method], $args);
            }
        } catch (\Exception $e) {
            // Helper not found, return empty string
        }
        return '';
    }
}

/**
 * Abstract view helper
 */
abstract class Zend_View_Helper_Abstract
{
    /**
     * @var Zend_View
     */
    public $view;

    /**
     * Set view
     */
    public function setView(Zend_View $view)
    {
        $this->view = $view;
    }
}
