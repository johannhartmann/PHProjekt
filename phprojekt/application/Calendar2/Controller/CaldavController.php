<?php
/**
 * Calendar2 CalDAV Controller - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */
namespace Application\Calendar2\Controller;

use Application\Default\Controller\IndexController as DefaultIndexController;
use Laminas\View\Model\ViewModel;

/**
 * Calendar2 Module CalDAV Controller
 *
 * Handles CalDAV protocol for calendar synchronization
 */
class CaldavController extends DefaultIndexController
{
    /**
     * Override checkAuthentication
     * We use HTTP authentication instead of normal session-based auth
     *
     * @return void
     */
    public function checkAuthentication()
    {
        try {
            if (array_key_exists('PHP_AUTH_USER', $_SERVER)) {
                \Phprojekt_Auth::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                $_SERVER['PHP_AUTH_USER'] = strtolower(\Phprojekt_Auth::getRealUser()->username);
            }
        } catch (\Phprojekt_Auth_Exception $e) {
            // Delete stack trace to avoid logging user password
            throw new \Phprojekt_Auth_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Fire up the SabreDAV server with custom backends
     *
     * This implements CalDAV protocol for calendar synchronization
     *
     * @return void
     */
    public function indexAction()
    {
        // Disable view rendering for DAV protocol
        $this->layout('layout/empty');

        // Backends
        $authBackend = new \Calendar2_Helper_Auth();
        $principalBackend = new \Phprojekt_CalDAV_PrincipalBackend();
        $calendarBackend = new \Calendar2_CalDAV_CalendarBackend();

        // Directory tree
        $tree = array(
            new \Sabre_DAVACL_PrincipalCollection($principalBackend),
            new \Sabre_CalDAV_CalendarRootNode($principalBackend, $calendarBackend)
        );
        $server = new \Sabre_DAV_Server($tree);

        $server->setBaseUri('/index.php/Calendar2/caldav/index');

        // Authentication plugin
        $authPlugin = new \Sabre_DAV_Auth_Plugin($authBackend, 'CalDAV');
        $server->addPlugin($authPlugin);

        // CalDAV plugin
        $caldavPlugin = new \Sabre_CalDAV_Plugin();
        $server->addPlugin($caldavPlugin);

        // ACL plugin
        $aclPlugin = new \Sabre_DAVACL_Plugin();
        $server->addPlugin($aclPlugin);

        // Support for html frontend
        $browser = new \Sabre_DAV_Browser_Plugin();
        $server->addPlugin($browser);

        // Execute DAV server
        $server->exec();

        // Return empty view (DAV handles its own output)
        return new ViewModel();
    }
}
