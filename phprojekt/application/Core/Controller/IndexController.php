<?php
/**
 * Core Index Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Core\Controller;

use Application\Default\Controller\IndexController as DefaultIndexController;
use Laminas\View\Model\JsonModel;

/**
 * Core Index Controller
 *
 * Base controller for Core module with admin permission checking
 */
class IndexController extends DefaultIndexController
{
    /**
     * Pre-dispatch - check admin permissions
     *
     * There are only a few actions that a normal user can do requesting the Core controller.
     * This function checks them and allows the action or not.
     * If not allowed, returns 401 Unauthorized.
     *
     * @return void|\Laminas\Http\Response
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Phprojekt_Auth::isAdminUser()) {
            $valid = false;

            // Add exceptions for public calls into the Core
            $controller = strtolower($this->params('controller'));
            $action = $this->params('action');

            // Allow certain actions for non-admin users
            if ($controller == 'history' && $action == 'jsonList') {
                $valid = true;
            } else if ($controller == 'module' && $action == 'jsonGetGlobalModules') {
                $valid = true;
            } else if ($controller == 'role' && $action == 'jsonGetModulesAccess') {
                $valid = true;
            } else if ($controller == 'user' && $action == 'jsonGetUsers') {
                $valid = true;
            } else if ($controller == 'user' && $action == 'jsonGetProxyableUsers') {
                $valid = true;
            } else if ($controller == 'tab' && $action == 'jsonList') {
                $valid = true;
            } else if ($controller == 'setting') {
                $valid = true;
            } else if ($controller == 'upgrade') {
                $valid = true;
            }

            if (!$valid) {
                $response = $this->getResponse();
                $response->setStatusCode(401);
                $response->getHeaders()->addHeaderLine('WWW-Authenticate', 'AdminRequired');
                $response->sendHeaders();
                exit;
            }
        }
    }

    /**
     * Get Core module model object
     *
     * @return \Phprojekt_Model_Interface
     */
    public function getModelObject()
    {
        static $moduleName = null;

        if (is_null($moduleName)) {
            $controllerName = $this->params('controller');
            $moduleName = ucfirst($controllerName);
            $moduleName = "Phprojekt_" . $moduleName . "_" . $moduleName;
        }

        if (\Phprojekt_Loader::tryToLoadLibClass($moduleName)) {
            $db = \Phprojekt::getInstance()->getDb();
            return new $moduleName($db);
        } else {
            throw new \Exception('No model object could be found');
        }
    }

    /**
     * Set current project ID - Core always uses INVISIBLE_ROOT
     *
     * @return void
     */
    public function setCurrentProjectId()
    {
        \Phprojekt::setCurrentProjectId(self::INVISIBLE_ROOT);
    }
}
