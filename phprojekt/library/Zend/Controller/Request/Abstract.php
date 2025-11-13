<?php
/**
 * Compatibility shim for Zend_Controller_Request_Abstract
 */

abstract class Zend_Controller_Request_Abstract
{
    /**
     * Parameters
     * @var array
     */
    protected $_params = [];

    /**
     * Dispatched flag
     * @var bool
     */
    protected $_dispatched = false;

    /**
     * Module name
     * @var string
     */
    protected $_moduleName;

    /**
     * Controller name
     * @var string
     */
    protected $_controllerName;

    /**
     * Action name
     * @var string
     */
    protected $_actionName;

    /**
     * Set parameter
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * Get parameter
     */
    public function getParam($key, $default = null)
    {
        return $this->_params[$key] ?? $default;
    }

    /**
     * Get all parameters
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set parameters
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Set dispatched flag
     */
    public function setDispatched($flag = true)
    {
        $this->_dispatched = (bool)$flag;
        return $this;
    }

    /**
     * Is dispatched
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }

    /**
     * Set module name
     */
    public function setModuleName($name)
    {
        $this->_moduleName = $name;
        return $this;
    }

    /**
     * Get module name
     */
    public function getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * Set controller name
     */
    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }

    /**
     * Get controller name
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }

    /**
     * Set action name
     */
    public function setActionName($name)
    {
        $this->_actionName = $name;
        return $this;
    }

    /**
     * Get action name
     */
    public function getActionName()
    {
        return $this->_actionName;
    }
}

// Create alias for compatibility
if (!class_exists('Zend_Controller_Request_Http', false)) {
    // If Http class hasn't been loaded yet, create it as an alias
    class_alias('Zend_Controller_Request_Abstract', 'Zend_Controller_Request_Http_Alias');
}
