<?php
/**
 * Compatibility shim for Zend_Controller_Request_Http
 */

use Laminas\Http\PhpEnvironment\Request as LaminasRequest;

class Zend_Controller_Request_Http extends LaminasRequest
{
    /**
     * Parameters
     * @var array
     */
    protected $_params = [];

    /**
     * Module name
     * @var string
     */
    protected $_moduleName = 'default';

    /**
     * Controller name
     * @var string
     */
    protected $_controllerName = 'index';

    /**
     * Action name
     * @var string
     */
    protected $_actionName = 'index';

    /**
     * Constructor
     */
    public function __construct($uri = null)
    {
        parent::__construct();

        if ($uri) {
            $this->setUri($uri);
        }

        // Parse request
        $this->_params = array_merge($_GET, $_POST);
    }

    /**
     * Get parameter
     */
    public function getParam($key, $default = null)
    {
        return $this->_params[$key] ?? $default;
    }

    /**
     * Set parameter
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
        return $this;
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
     * Get module name
     */
    public function getModuleName()
    {
        return $this->_moduleName;
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
     * Get controller name
     */
    public function getControllerName()
    {
        return $this->_controllerName;
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
     * Get action name
     */
    public function getActionName()
    {
        return $this->_actionName;
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
     * Check if request is AJAX
     */
    public function isXmlHttpRequest()
    {
        return $this->isXhr();
    }
}
