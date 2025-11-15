<?php
/**
 * Bootstrap file for setup.
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * @category   PHProjekt
 * @package    Htdocs
 * @subpackage Setup
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 * @link       http://www.phprojekt.com
 * @since      File available since Release 6.0
 * @version    Release: 6.1.0
 * @author     Gustavo Solt <solt@mayflower.de>
 */

/**
 * @ignore
 */
define('PHPR_CONFIG_SECTION', 'production');

/**
 * @ignore
 */
define('PHPR_ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('PHPR_CORE_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'htdocs');
define('PHPR_USER_CORE_PATH', PHPR_CORE_PATH);
define('PHPR_LIBRARY_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library');
if (!defined('PHPR_CONFIG_FILE')) {
    define('PHPR_CONFIG_FILE', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'configuration.php');
}

set_include_path('.' . PATH_SEPARATOR
    . PHPR_LIBRARY_PATH . PATH_SEPARATOR
    . PHPR_CORE_PATH . PATH_SEPARATOR
    . get_include_path());

require_once PHPR_ROOT_PATH . '/vendor/autoload.php';
spl_autoload_register(array('Phprojekt_Loader', 'autoload'), true, false);

ini_set('max_execution_time', 0);
error_reporting(-1);

// Set the timezone to UTC
date_default_timezone_set('UTC');

// Start Laminas session to handle all session stuff
\Laminas\Session\SessionManager::getDefaultManager()->start();

$view = new \Laminas\View\Renderer\PhpRenderer();
$view->resolver()->addPath(PHPR_CORE_PATH . '/Setup/Views/dojo/');

// Note: Laminas MVC uses different architecture than Zend Framework 1
// The front controller pattern has been replaced with the event-driven MVC in Laminas
// This bootstrap maintains compatibility while using Laminas components

try {
    // Use a simple routing approach for setup
    $request = new \Laminas\Http\PhpEnvironment\Request();
    $module = $request->getQuery('module', 'Setup');
    $controller = $request->getQuery('controller', 'Index');
    $action = $request->getQuery('action', 'index');

    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerFile = PHPR_CORE_PATH . '/' . $module . '/Controllers/' . $controllerClass . '.php';

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerInstance = new $controllerClass();
        $actionMethod = $action . 'Action';
        if (method_exists($controllerInstance, $actionMethod)) {
            $controllerInstance->$actionMethod();
        }
    }
} catch (Exception $error) {
    echo "Caught exception: " . $error->getFile() . ':' . $error->getLine() . "\n";
    echo '<br/>' . $error->getMessage();
    echo '<pre>' . $error->getTraceAsString() . '</pre>';
}
