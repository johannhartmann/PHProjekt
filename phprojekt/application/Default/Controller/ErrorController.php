<?php
/**
 * Laminas MVC Error Controller
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response as HttpResponse;

/**
 * Error Controller - Proper Laminas MVC implementation
 *
 * Handles application errors and exceptions with support for:
 * - HTML error pages
 * - JSON error responses (for AJAX requests)
 * - Proper HTTP status codes
 * - Logging integration
 */
class ErrorController extends AbstractActionController
{
    /**
     * Error action - handles all application errors
     *
     * @return ViewModel|JsonModel
     */
    public function errorAction()
    {
        // Get error information from request
        $error = $this->params()->fromRoute('error');
        if (!$error) {
            $error = $this->params()->fromQuery('error');
        }

        // Get exception from MvcEvent if available
        $exception = null;
        $statusCode = 500;
        $message = 'Internal Server Error';

        $viewModel = $this->getEvent()->getViewModel();
        $mvcEvent = $this->getEvent();

        // Get exception from event
        if ($mvcEvent->getParam('exception')) {
            $exception = $mvcEvent->getParam('exception');
            $message = $exception->getMessage();

            // Determine status code from exception
            if (method_exists($exception, 'getCode') && $exception->getCode() >= 400 && $exception->getCode() < 600) {
                $statusCode = $exception->getCode();
            } elseif (method_exists($exception, 'getHttpCode')) {
                $statusCode = $exception->getHttpCode();
            }
        }

        // Check for 404 errors
        if ($mvcEvent->getError() === 'error-router-no-match' ||
            $mvcEvent->getError() === 'error-controller-not-found' ||
            $mvcEvent->getError() === 'error-controller-invalid' ||
            $mvcEvent->getError() === 'error-action-not-found') {
            $statusCode = 404;
            $message = 'Page Not Found';

            if ($exception) {
                $message = 'The URL ' . $this->getRequest()->getUri()->getPath() . ' does not exist';
            }
        }

        // Set HTTP response code
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        // Log the error
        $this->logError($exception, $statusCode, $message);

        // Check if this is an AJAX request
        if ($this->getRequest()->isXmlHttpRequest() ||
            $this->getRequest()->getHeader('Accept')->match('*/json')) {

            // Return JSON error response
            return new JsonModel([
                'type' => 'error',
                'message' => $message,
                'code' => $statusCode,
                'success' => false
            ]);
        }

        // Return HTML error page
        $viewModel = new ViewModel([
            'message' => $message,
            'exception' => $exception,
            'statusCode' => $statusCode,
            'displayExceptions' => $this->displayExceptions(),
        ]);

        // Use different template for 404 vs other errors
        if ($statusCode == 404) {
            $viewModel->setTemplate('error/404');
        } else {
            $viewModel->setTemplate('error/index');
        }

        return $viewModel;
    }

    /**
     * Log error to application log
     *
     * @param \Exception|null $exception
     * @param int $statusCode
     * @param string $message
     * @return void
     */
    protected function logError($exception, $statusCode, $message)
    {
        try {
            // Get logger from service manager if available
            $logger = null;
            $serviceManager = $this->getEvent()->getApplication()->getServiceManager();

            if ($serviceManager->has('Phprojekt')) {
                $phprojekt = $serviceManager->get('Phprojekt');
                if (method_exists($phprojekt, 'getLog')) {
                    $logger = $phprojekt->getLog();
                }
            }

            if (!$logger && class_exists('Phprojekt')) {
                // Fallback to singleton if service manager doesn't have it
                $logger = \Phprojekt::getInstance()->getLog();
            }

            if ($logger) {
                if ($exception) {
                    $logMessage = sprintf(
                        "HTTP %d Error: %s\n%s\n%s",
                        $statusCode,
                        $message,
                        $exception->getMessage(),
                        $exception->getTraceAsString()
                    );
                    $logger->err($logMessage);
                } else {
                    $logger->err(sprintf("HTTP %d Error: %s", $statusCode, $message));
                }
            }
        } catch (\Exception $e) {
            // Fail silently if logging fails
            error_log("Failed to log error: " . $e->getMessage());
        }
    }

    /**
     * Check if exceptions should be displayed
     *
     * @return bool
     */
    protected function displayExceptions()
    {
        $config = $this->getEvent()->getApplication()->getServiceManager()->get('config');
        return isset($config['view_manager']['display_exceptions'])
            ? $config['view_manager']['display_exceptions']
            : false;
    }

    /**
     * Not found action - 404 handler
     *
     * @return ViewModel|JsonModel
     */
    public function notFoundAction()
    {
        $response = $this->getResponse();
        $response->setStatusCode(404);

        $message = 'Page Not Found - The requested URL was not found on this server';

        // Check if this is an AJAX request
        if ($this->getRequest()->isXmlHttpRequest()) {
            return new JsonModel([
                'type' => 'error',
                'message' => $message,
                'code' => 404,
                'success' => false
            ]);
        }

        return new ViewModel([
            'message' => $message,
            'statusCode' => 404,
        ]);
    }
}
