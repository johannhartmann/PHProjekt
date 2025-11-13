<?php
/**
 * Compatibility shim for Zend_Controller_Action_HelperBroker
 */

class Zend_Controller_Action_HelperBroker
{
    /**
     * Registered helpers
     * @var array
     */
    protected $_helpers = [];

    /**
     * Static helpers
     * @var array
     */
    protected static $_staticHelpers = [];

    /**
     * Helper paths
     * @var array
     */
    protected static $_helperPaths = [];

    /**
     * Action controller
     * @var Zend_Controller_Action
     */
    protected $_actionController;

    /**
     * Constructor
     */
    public function __construct(Zend_Controller_Action $actionController = null)
    {
        $this->_actionController = $actionController;
        $this->_loadDefaultHelpers();
    }

    /**
     * Load default helpers
     */
    protected function _loadDefaultHelpers()
    {
        // Register common helpers
        $this->_helpers['redirector'] = new Zend_Controller_Action_Helper_Redirector();
        $this->_helpers['viewRenderer'] = new Zend_Controller_Action_Helper_ViewRenderer();
        $this->_helpers['url'] = new Zend_Controller_Action_Helper_Url();
        $this->_helpers['json'] = new Zend_Controller_Action_Helper_Json();
    }

    /**
     * Get helper
     */
    public function __get($name)
    {
        return $this->getHelper($name);
    }

    /**
     * Get helper by name
     */
    public function getHelper($name)
    {
        $name = ucfirst($name);

        if (!isset($this->_helpers[$name]) && !isset($this->_helpers[strtolower($name)])) {
            $className = 'Zend_Controller_Action_Helper_' . $name;
            if (class_exists($className)) {
                $this->_helpers[strtolower($name)] = new $className();
            } else {
                throw new Zend_Controller_Action_Exception("Helper '$name' not found");
            }
        }

        return $this->_helpers[strtolower($name)] ?? $this->_helpers[$name];
    }

    /**
     * Check if helper exists
     */
    public function hasHelper($name)
    {
        $name = strtolower($name);
        return isset($this->_helpers[$name]);
    }

    /**
     * Add helper to instance
     */
    public function addInstanceHelper(Zend_Controller_Action_Helper_Abstract $helper)
    {
        $name = $helper->getName();
        $this->_helpers[strtolower($name)] = $helper;
        return $this;
    }

    /**
     * Remove helper
     */
    public function removeHelper($name)
    {
        $name = strtolower($name);
        if (isset($this->_helpers[$name])) {
            unset($this->_helpers[$name]);
        }
        return $this;
    }

    /**
     * Get static helper
     */
    public static function getStaticHelper($name)
    {
        $name = strtolower($name);

        if (!isset(self::$_staticHelpers[$name])) {
            $className = 'Zend_Controller_Action_Helper_' . ucfirst($name);
            if (class_exists($className)) {
                self::$_staticHelpers[$name] = new $className();
            } else {
                throw new Zend_Controller_Action_Exception("Helper '$name' not found");
            }
        }

        return self::$_staticHelpers[$name];
    }

    /**
     * Add helper (static method for global registration)
     */
    public static function addHelper(Zend_Controller_Action_Helper_Abstract $helper)
    {
        $name = strtolower($helper->getName());
        self::$_staticHelpers[$name] = $helper;
    }

    /**
     * Add path for helper loading
     */
    public static function addPath($path, $prefix = null)
    {
        self::$_helperPaths[] = [
            'path' => $path,
            'prefix' => $prefix
        ];
    }

    /**
     * Get action controller
     */
    public function getActionController()
    {
        return $this->_actionController;
    }

    /**
     * Set action controller
     */
    public function setActionController(Zend_Controller_Action $actionController = null)
    {
        $this->_actionController = $actionController;
        return $this;
    }

    /**
     * Notify pre-dispatch
     */
    public function notifyPreDispatch()
    {
        foreach ($this->_helpers as $helper) {
            $helper->preDispatch();
        }
    }

    /**
     * Notify post-dispatch
     */
    public function notifyPostDispatch()
    {
        foreach ($this->_helpers as $helper) {
            $helper->postDispatch();
        }
    }
}
