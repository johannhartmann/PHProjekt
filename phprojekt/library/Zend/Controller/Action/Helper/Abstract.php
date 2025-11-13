<?php
/**
 * Compatibility shim for Zend_Controller_Action_Helper_Abstract
 */

abstract class Zend_Controller_Action_Helper_Abstract
{
    /**
     * Action controller
     * @var Zend_Controller_Action
     */
    protected $_actionController;

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
     * Set action controller
     */
    public function setActionController(Zend_Controller_Action $actionController = null)
    {
        $this->_actionController = $actionController;
        if ($actionController !== null) {
            $this->_request = $actionController->getRequest();
            $this->_response = $actionController->getResponse();
        }
        return $this;
    }

    /**
     * Get action controller
     */
    public function getActionController()
    {
        return $this->_actionController;
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
     * Get helper name
     */
    public function getName()
    {
        $class = get_class($this);
        if (strpos($class, '_') !== false) {
            return strtolower(substr($class, strrpos($class, '_') + 1));
        }
        return strtolower($class);
    }

    /**
     * Hook for pre-dispatch
     */
    public function preDispatch()
    {
    }

    /**
     * Hook for post-dispatch
     */
    public function postDispatch()
    {
    }

    /**
     * Initialize
     */
    public function init()
    {
    }
}
