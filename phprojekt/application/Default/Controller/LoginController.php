<?php
/**
 * Login Controller - Laminas MVC
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
use Laminas\Session\Container as SessionContainer;

/**
 * Login Controller - Handles user authentication
 */
class LoginController extends AbstractActionController
{
    /**
     * Display login form
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        // Clear any previous response data
        $response = $this->getResponse();
        $response->getHeaders()->clearHeaders();
        $response->setContent('');

        // Get configuration
        $phprojekt = $this->getPhprojektInstance();
        $config = $phprojekt->getConfig();

        return new ViewModel([
            'compressedDojo' => (bool) $config->compressedDojo ?? false,
        ]);
    }

    /**
     * Process login form submission
     *
     * OPTIONAL request parameters:
     * - string username   Username for login
     * - string password   Password for login
     * - string hash       Hash URL for redirect after login
     * - string keepLogged 1 if user wants to stay logged in
     * - string domain     LDAP domain (if applicable)
     *
     * @return ViewModel
     */
    public function loginAction()
    {
        // Get and sanitize parameters
        $username   = \Cleaner::sanitize('xss', $this->params()->fromPost('username', null));
        $password   = \Cleaner::sanitize('xss', $this->params()->fromPost('password', null));
        $hash       = \Cleaner::sanitize('xss', $this->params()->fromPost('hash', null));
        $legacy     = \Cleaner::sanitize('xss', $this->params()->fromPost('legacy', null));
        $keepLogged = (int) $this->params()->fromPost('keepLogged', 0);
        $keepLogged = ($keepLogged == 1);
        $loginServer = $this->params()->fromPost('domain', null);

        // Get configuration
        $phprojekt = $this->getPhprojektInstance();
        $config = $phprojekt->getConfig();

        try {
            // Attempt authentication
            $success = \Phprojekt_Auth::login(
                $username,
                $password,
                ['keepLogged' => $keepLogged, 'loginServer' => $loginServer]
            );

            if ($success === true) {
                // Check if legacy login is restricted to admins
                if (!is_null($legacy) && !\Phprojekt_Auth::isAdminUser()) {
                    \Phprojekt_Auth::logout();

                    return new ViewModel([
                        'message'  => 'Sorry, legacy login is only allowed for administrators',
                        'username' => $username,
                        'hash'     => $hash,
                        'compressedDojo' => (bool) ($config->compressedDojo ?? false),
                    ]);
                }

                // Set up session
                $pageNamespace = new SessionContainer('page');

                // Send frontend notification
                $frontendMessage = new \Phprojekt_Notification();
                $frontendMessage->setControllProcess(\Phprojekt_Notification::LAST_ACTION_LOGIN);
                $frontendMessage->saveFrontendMessage();

                // Clean up unused files
                \Default_Helpers_Upload::cleanUnusedFiles();

                // Set page type
                if (!is_null($legacy)) {
                    $pageNamespace->type = 'legacy';
                } else {
                    $pageNamespace->type = 'timecard';
                }

                // Redirect to application
                return $this->redirect()->toUrl('../../index.php' . $hash);
            }
        } catch (\Phprojekt_Auth_Exception $error) {
            // Authentication failed, show login form with error
            return new ViewModel([
                'message'  => $error->getMessage(),
                'username' => $username,
                'hash'     => $hash,
                'compressedDojo' => (bool) ($config->compressedDojo ?? false),
            ]);
        }

        // Fallback - should not reach here
        return $this->redirect()->toRoute('home');
    }

    /**
     * JSON login endpoint
     *
     * OPTIONAL request parameters:
     * - string username Username for login
     * - string password Password for login
     * - string legacy   Whether this is a legacy login
     *
     * Returns JSON with:
     * - type    => 'success' or 'error'
     * - message => Success or error message
     *
     * @return JsonModel
     */
    public function jsonLoginAction()
    {
        // Get and sanitize parameters
        $username = \Cleaner::sanitize('xss', $this->params()->fromPost('username', null));
        $password = \Cleaner::sanitize('xss', $this->params()->fromPost('password', null));
        $legacy   = \Cleaner::sanitize('xss', $this->params()->fromPost('legacy', false));

        try {
            // Attempt authentication
            $success = \Phprojekt_Auth::login($username, $password);

            if ($success === true) {
                // Set up session
                $pageNamespace = new SessionContainer('page');

                if ($legacy !== false) {
                    $pageNamespace->type = 'legacy';
                } else {
                    $pageNamespace->type = 'timecard';
                }

                return new JsonModel([
                    'type'    => 'success',
                    'message' => '',
                ]);
            }
        } catch (\Phprojekt_Auth_Exception $error) {
            return new JsonModel([
                'type'    => 'error',
                'message' => $error->getMessage(),
            ]);
        }

        // Fallback error
        return new JsonModel([
            'type'    => 'error',
            'message' => 'Authentication failed',
        ]);
    }

    /**
     * Logout action
     *
     * Logs out the user and redirects to login page
     *
     * @return \Laminas\Http\Response
     */
    public function logoutAction()
    {
        // Send frontend notification
        $frontendMessage = new \Phprojekt_Notification();
        $frontendMessage->setControllProcess(\Phprojekt_Notification::LAST_ACTION_LOGOUT);
        $frontendMessage->saveFrontendMessage();

        // Perform logout
        \Phprojekt_Auth::logout();

        // Redirect to login page
        return $this->redirect()->toUrl('../../index.php');
    }

    /**
     * Get Phprojekt instance
     *
     * @return \Phprojekt
     */
    protected function getPhprojektInstance()
    {
        // Try to get from service manager first
        $serviceManager = $this->getEvent()->getApplication()->getServiceManager();

        if ($serviceManager->has('Phprojekt')) {
            return $serviceManager->get('Phprojekt');
        }

        // Fallback to singleton
        return \Phprojekt::getInstance();
    }
}
