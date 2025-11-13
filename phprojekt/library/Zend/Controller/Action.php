<?php
/**
 * Compatibility shim for Zend_Controller_Action
 */

abstract class Zend_Controller_Action
{
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
     * Action helpers
     * @var array
     */
    protected $_helper;

    /**
     * View object
     * @var Zend_View
     */
    public $view;

    /**
     * Constructor
     */
    public function __construct(
        Zend_Controller_Request_Http $request = null,
        Zend_Controller_Response_Http $response = null,
        $invokeArgs = []
    ) {
        $this->_request = $request ?: new Zend_Controller_Request_Http();
        $this->_response = $response ?: new Zend_Controller_Response_Http();

        // Initialize helper broker
        $this->_helper = new Zend_Controller_Action_HelperBroker();

        // Initialize view
        $this->view = new Zend_View();

        // Call init hook
        $this->init();
    }

    /**
     * Init hook - override in subclasses
     */
    public function init()
    {
    }

    /**
     * Pre-dispatch hook
     */
    public function preDispatch()
    {
    }

    /**
     * Post-dispatch hook
     */
    public function postDispatch()
    {
    }

    /**
     * Dispatch an action
     */
    public function dispatch($action)
    {
        $this->preDispatch();

        if (!$this->_request->isDispatched()) {
            return;
        }

        $actionMethod = $action . 'Action';

        if (method_exists($this, $actionMethod)) {
            $this->$actionMethod();
        } else {
            throw new Zend_Controller_Action_Exception(
                "Action '$action' does not exist",
                404
            );
        }

        $this->postDispatch();
    }

    /**
     * Get request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Get a request parameter
     */
    public function getParam($key, $default = null)
    {
        return $this->_request->getParam($key, $default);
    }

    /**
     * Get all request parameters
     */
    public function getAllParams()
    {
        return $this->_request->getParams();
    }

    /**
     * Set a request parameter
     */
    public function setParam($key, $value)
    {
        $this->_request->setParam($key, $value);
        return $this;
    }

    /**
     * Forward to another action
     */
    public function _forward($action, $controller = null, $module = null, array $params = null)
    {
        $this->_request->setActionName($action);

        if ($controller !== null) {
            $this->_request->setControllerName($controller);
        }

        if ($module !== null) {
            $this->_request->setModuleName($module);
        }

        if ($params !== null) {
            $this->_request->setParams($params);
        }

        $this->_request->setDispatched(false);
    }

    /**
     * Redirect to a URL
     */
    protected function _redirect($url, array $options = [])
    {
        $code = isset($options['code']) ? $options['code'] : 302;

        $this->_response->setStatusCode($code);
        $this->_response->getHeaders()->addHeaderLine('Location', $url);

        if (isset($options['exit']) && $options['exit']) {
            $this->_response->send();
            exit;
        }
    }

    /**
     * Check if request has a parameter
     */
    public function hasParam($key)
    {
        return $this->_request->getParam($key) !== null;
    }
}

/**
 * Exception class
 */
class Zend_Controller_Action_Exception extends Exception
{
}
