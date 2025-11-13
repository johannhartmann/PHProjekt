<?php
/**
 * Compatibility shim for Zend_Controller_Plugin_Abstract
 */

abstract class Zend_Controller_Plugin_Abstract
{
    /**
     * Request object
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Response object
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Set request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Get request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Get response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Called before routing
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Called after routing
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Called before dispatch loop
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Called before an action is dispatched
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Called after an action is dispatched
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
    }

    /**
     * Called after dispatch loop
     */
    public function dispatchLoopShutdown()
    {
    }
}
