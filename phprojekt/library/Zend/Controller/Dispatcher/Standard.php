<?php
/**
 * Compatibility shim for Zend_Controller_Dispatcher_Standard
 */

class Zend_Controller_Dispatcher_Standard
{
    /**
     * Default module
     * @var string
     */
    protected $_defaultModule = 'default';

    /**
     * Default controller
     * @var string
     */
    protected $_defaultController = 'index';

    /**
     * Default action
     * @var string
     */
    protected $_defaultAction = 'index';

    /**
     * Dispatched flag
     * @var bool
     */
    protected $_dispatched = false;

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
     * Set default controller
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = $controller;
        return $this;
    }

    /**
     * Get default controller
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * Set default action
     */
    public function setDefaultAction($action)
    {
        $this->_defaultAction = $action;
        return $this;
    }

    /**
     * Get default action
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Is dispatchable
     */
    public function isDispatchable(Zend_Controller_Request_Http $request)
    {
        return true;
    }

    /**
     * Dispatch
     */
    public function dispatch(Zend_Controller_Request_Http $request, Zend_Controller_Response_Http $response)
    {
        $this->_dispatched = true;

        $module = $request->getModuleName() ?: $this->_defaultModule;
        $controller = $request->getControllerName() ?: $this->_defaultController;
        $action = $request->getActionName() ?: $this->_defaultAction;

        // Build controller class name
        $controllerClass = ucfirst($module) . '_' . ucfirst($controller) . 'Controller';

        if (!class_exists($controllerClass)) {
            throw new Zend_Controller_Dispatcher_Exception("Controller '$controllerClass' not found");
        }

        // Instantiate controller
        $controllerInstance = new $controllerClass($request, $response);

        // Dispatch action
        $actionMethod = $action . 'Action';
        if (!method_exists($controllerInstance, $actionMethod)) {
            throw new Zend_Controller_Dispatcher_Exception("Action '$actionMethod' not found in controller '$controllerClass'");
        }

        $controllerInstance->$actionMethod();

        return $response;
    }
}

/**
 * Dispatcher exception
 */
class Zend_Controller_Dispatcher_Exception extends Exception
{
}
