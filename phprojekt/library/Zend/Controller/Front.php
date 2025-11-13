<?php
/**
 * Compatibility shim for Zend_Controller_Front
 */

class Zend_Controller_Front
{
    /**
     * Singleton instance
     * @var Zend_Controller_Front
     */
    protected static $_instance;

    /**
     * Request object
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response object
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Dispatcher
     * @var mixed
     */
    protected $_dispatcher;

    /**
     * Router
     * @var mixed
     */
    protected $_router;

    /**
     * Plugins
     * @var array
     */
    protected $_plugins = [];

    /**
     * Module directories
     * @var array
     */
    protected $_moduleDirectories = [];

    /**
     * Default module
     * @var string
     */
    protected $_defaultModule = 'default';

    /**
     * Module controller directory name
     * @var string
     */
    protected $_moduleControllerDirectoryName = 'controllers';

    /**
     * Parameters
     * @var array
     */
    protected $_params = [];

    /**
     * Base URL
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Get request
     */
    public function getRequest()
    {
        if ($this->_request === null) {
            $this->_request = new Zend_Controller_Request_Http();
        }
        return $this->_request;
    }

    /**
     * Set request
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get response
     */
    public function getResponse()
    {
        if ($this->_response === null) {
            $this->_response = new Zend_Controller_Response_Http();
        }
        return $this->_response;
    }

    /**
     * Set response
     */
    public function setResponse(Zend_Controller_Response_Http $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Set dispatcher
     */
    public function setDispatcher($dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Get dispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    /**
     * Get router
     */
    public function getRouter()
    {
        if ($this->_router === null) {
            // Create a simple router stub
            $this->_router = new Zend_Controller_Router_Rewrite();
        }
        return $this->_router;
    }

    /**
     * Register plugin
     */
    public function registerPlugin($plugin)
    {
        $this->_plugins[] = $plugin;
        return $this;
    }

    /**
     * Set default module
     */
    public function setDefaultModule($module)
    {
        $this->_defaultModule = $module;
        return $this;
    }

    /**
     * Get default module
     */
    public function getDefaultModule()
    {
        return $this->_defaultModule;
    }

    /**
     * Set module controller directory name
     */
    public function setModuleControllerDirectoryName($name)
    {
        $this->_moduleControllerDirectoryName = $name;
        return $this;
    }

    /**
     * Add module directory
     */
    public function addModuleDirectory($path)
    {
        $this->_moduleDirectories[] = $path;
        return $this;
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
     * Get parameter
     */
    public function getParam($key)
    {
        return $this->_params[$key] ?? null;
    }

    /**
     * Set base URL
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
        return $this;
    }

    /**
     * Get base URL
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }
}

/**
 * Router stub
 */
class Zend_Controller_Router_Rewrite
{
    protected $_routes = [];

    public function addRoute($name, $route)
    {
        $this->_routes[$name] = $route;
        return $this;
    }

    public function getRoutes()
    {
        return $this->_routes;
    }
}
