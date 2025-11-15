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
 * @copyright  Copyright (c) 2011 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Stdlib\RequestInterface;

/**
 * This Laminas controller plugin is used to implement the phprojekt extensions.
 *
 * It is also used to check whether we need to redirect the user to the
 * migration screen. This is because the check uses extensions, but must be done
 * before init is called, as the modules will assume that they have a current
 * database.
 */
class Phprojekt_ExtensionsPlugin extends AbstractPlugin
{
    private $_extensions;

    /**
     * Initializes the Phprojekt_Extensions object for the ExtensionsPlugin class..
     *
     * The __construct() method is the constructor for the Phprojekt_ExtensionsPlugin class.
     * It initializes the _extensions property by creating a new instance of the Phprojekt_Extensions class, passing the PHPR_CORE_PATH constant as an argument.
     * @return void The constructor does not return anything, it only initializes the _extensions property.
     * @note This method modifies global state.
     * @see Phprojekt_Extensions
     */
    public function __construct()
    {
        /* initialize PHPRojekt Extensions */
        $this->_extensions = new Phprojekt_Extensions(PHPR_CORE_PATH);
    }

    public function routeShutdown(RequestInterface $request)
    {
        /* Redirect to the upgrade controller if an upgrade is neccessary */
        if (Phprojekt_Auth::isLoggedIn()
                && ($request->getModuleName() != 'Core'
                    || $request->getControllerName() != 'Upgrade')
                && ($request->getControllerName() != 'Login'
                    || $request->getActionName() != 'logout')) {
            $migration = new Phprojekt_Migration($this->_extensions);
            if ($migration->needsUpgrade()) {
                $this->_request->setModuleName('Core');
                $this->_request->setControllerName('Upgrade');
                $this->_request->setActionName('index');
            }
        }
    }

    /**
     * Initializes all registered extensions before the main controller dispatch..
     *
     * This method is called before the main controller dispatch.
     * It iterates through all registered extensions and calls their `init()` method, allowing the extensions to perform any necessary initialization tasks.
     *
     * @param RequestInterface $request The current controller request object.
     * @note This method modifies global state.
     */
    public function preDispatch(RequestInterface $request)
    {
        // Call the init method on every extension
        $this->_extensions->init();
    }
}
