<?php
/**
 * Compatibility shim for Zend_Controller_Plugin_ErrorHandler
 */

class Zend_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract
{
    /**
     * Error handler module
     * @var string
     */
    protected $_errorModule = 'default';

    /**
     * Error handler controller
     * @var string
     */
    protected $_errorController = 'error';

    /**
     * Error handler action
     * @var string
     */
    protected $_errorAction = 'error';

    /**
     * Whether plugin is enabled
     * @var bool
     */
    protected $_isInsideErrorHandlerLoop = false;

    /**
     * Exception types
     */
    const EXCEPTION_NO_ROUTE = 'EXCEPTION_NO_ROUTE';
    const EXCEPTION_NO_CONTROLLER = 'EXCEPTION_NO_CONTROLLER';
    const EXCEPTION_NO_ACTION = 'EXCEPTION_NO_ACTION';
    const EXCEPTION_OTHER = 'EXCEPTION_OTHER';

    /**
     * Set error handler module
     */
    public function setErrorHandlerModule($module)
    {
        $this->_errorModule = $module;
        return $this;
    }

    /**
     * Get error handler module
     */
    public function getErrorHandlerModule()
    {
        return $this->_errorModule;
    }

    /**
     * Set error handler controller
     */
    public function setErrorHandlerController($controller)
    {
        $this->_errorController = $controller;
        return $this;
    }

    /**
     * Get error handler controller
     */
    public function getErrorHandlerController()
    {
        return $this->_errorController;
    }

    /**
     * Set error handler action
     */
    public function setErrorHandlerAction($action)
    {
        $this->_errorAction = $action;
        return $this;
    }

    /**
     * Get error handler action
     */
    public function getErrorHandlerAction()
    {
        return $this->_errorAction;
    }

    /**
     * Post-dispatch hook
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Check for exceptions
        $response = $this->getResponse();

        if ($this->_isInsideErrorHandlerLoop) {
            return;
        }

        $exceptions = $response->getException();
        if (empty($exceptions) && !$response->isException()) {
            return;
        }

        $this->_handleError($request, $exceptions);
    }

    /**
     * Handle error
     */
    protected function _handleError($request, $exceptions)
    {
        $this->_isInsideErrorHandlerLoop = true;

        $exception = !empty($exceptions) ? $exceptions[0] : new Exception('Unknown error');

        $errorType = $this->_getErrorType($exception);

        // Forward to error handler
        $request->setModuleName($this->_errorModule);
        $request->setControllerName($this->_errorController);
        $request->setActionName($this->_errorAction);
        $request->setDispatched(false);

        // Set error parameters
        $request->setParam('error_handler', [
            'exception' => $exception,
            'type' => $errorType,
            'request' => clone $request
        ]);
    }

    /**
     * Get error type from exception
     */
    protected function _getErrorType($exception)
    {
        if ($exception instanceof Zend_Controller_Router_Exception) {
            return self::EXCEPTION_NO_ROUTE;
        }

        if ($exception instanceof Zend_Controller_Dispatcher_Exception) {
            return self::EXCEPTION_NO_CONTROLLER;
        }

        if ($exception instanceof Zend_Controller_Action_Exception) {
            if ($exception->getCode() == 404) {
                return self::EXCEPTION_NO_ACTION;
            }
        }

        return self::EXCEPTION_OTHER;
    }
}
