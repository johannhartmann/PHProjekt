<?php
/**
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

use Laminas\Http\Request as HttpRequest;
use Laminas\Http\Response as HttpResponse;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Uri\Http as HttpUri;

/**
 * Tests for Index Controller - Laminas MVC Test Base
 */
abstract class FrontInit extends DatabaseTest
{
    public $request      = null;
    public $response     = null;
    public $application  = null;
    public $config       = null;
    public $content      = null;
    public $error        = null;
    public $errormessage = null;

    /**
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Init the Laminas application for testing
     */
    public function __construct()
    {
        parent::__construct();

        // Get the global service manager (set up in Bootstrap.laminas.php)
        if (isset($GLOBALS['serviceManager'])) {
            $this->serviceManager = $GLOBALS['serviceManager'];
        } else {
            // Fallback: create application if not already initialized
            $appConfig = require PHPR_ROOT_PATH . '/config/application.config.php';
            $this->application = Application::init($appConfig);
            $this->serviceManager = $this->application->getServiceManager();
            $GLOBALS['serviceManager'] = $this->serviceManager;
        }

        // Create request and response objects
        $this->request  = new HttpRequest();
        $this->response = new HttpResponse();

        // Get config from Phprojekt (for backward compatibility)
        if (class_exists('Phprojekt') && Phprojekt::getInstance()) {
            $this->config = Phprojekt::getInstance()->getConfig();
            if ($this->config) {
                $this->config->language = "en";
            }
        }
    }

    /**
     * Set the URL for the request
     */
    public function setRequestUrl($url)
    {
        // Parse the URL to extract module, controller, action
        $parts = explode('/', trim($url, '/'));

        // Set the request URI
        $uri = new HttpUri();
        $uri->setPath('/' . $url);
        $this->request->setUri($uri);
        // Note: setRequestUri() was removed in Laminas - URI is set via setUri() above

        // Set the CSRF token
        if (class_exists('Phprojekt')) {
            $this->request->getQuery()->set('csrfToken', Phprojekt::createCsrfToken());
        }

        // Store URL parts for later use in getResponse()
        $this->request->_urlParts = $parts;
    }

    /**
     * Get the response by dispatching the request through Laminas MVC Application
     */
    public function getResponse()
    {
        ob_start();
        $this->error = false;

        try {
            // Create or get the application
            if (!$this->application) {
                $appConfig = require PHPR_ROOT_PATH . '/config/application.config.php';
                $this->application = Application::init($appConfig);
            }

            // Get the service manager
            $serviceManager = $this->application->getServiceManager();

            // Replace the request in service manager
            $serviceManager->setAllowOverride(true);
            $serviceManager->setService('Request', $this->request);
            $serviceManager->setService('Response', $this->response);
            $serviceManager->setAllowOverride(false);

            // Parse URL parts to set route parameters
            if (isset($this->request->_urlParts)) {
                $parts = $this->request->_urlParts;

                // Set route match parameters based on URL structure
                if (count($parts) >= 1) {
                    $module = ucfirst($parts[0]);
                    $controller = isset($parts[1]) ? ucfirst($parts[1]) : 'Index';
                    $action = isset($parts[2]) ? $parts[2] : 'index';

                    // Build route match
                    $routeMatch = new \Laminas\Router\Http\RouteMatch([
                        'module' => $module,
                        'controller' => $controller,
                        'action' => $action,
                    ]);

                    // Merge any query parameters into route match
                    $queryParams = $this->request->getQuery()->toArray();
                    foreach ($queryParams as $key => $value) {
                        $routeMatch->setParam($key, $value);
                    }

                    // Set the route match in MVC event
                    $event = $this->application->getMvcEvent();
                    $event->setRouteMatch($routeMatch);
                }
            }

            // Dispatch the application
            $this->application->run();

            // Get the response
            $this->response = $this->application->getResponse();
            $this->content = ob_get_contents();
            ob_end_clean();

            // If response has body content, use that instead
            if ($this->response && $this->response->getContent()) {
                $this->content = $this->response->getContent();
            }

        } catch (Exception $e) {
            /* make sure we end the output buffering in case of an exception */
            ob_end_clean();
            throw $e;
        }

        return $this->content;
    }

    /**
     * Reset the request and the response to allow another request to be done in this test
     */
    protected function _reset()
    {
        $this->request  = new HttpRequest();
        $this->response = new HttpResponse();
    }

    /**
     * Magic getter to provide backward compatibility for $this->front
     * (Some tests may still access $this->front for manual dispatch)
     */
    public function __get($name)
    {
        if ($name === 'front') {
            // Return a mock object that provides basic compatibility
            return new class($this) {
                private $test;

                public function __construct($test) {
                    $this->test = $test;
                }

                public function dispatch($request, $response) {
                    // Delegate to getResponse() which handles Laminas dispatch
                    return $this->test->getResponse();
                }
            };
        }

        return null;
    }
}
