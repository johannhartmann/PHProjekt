<?php
/**
 * Laminas MVC Application Bootstrap
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
 * @category  PHProjekt
 * @package   Htdocs
 * @copyright Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license   LGPL v3 (See LICENSE file)
 */

/**
 * This makes our life easier when dealing with paths.
 */
chdir(dirname(__DIR__));

/**
 * Section to use from configuration.php.
 */
define('PHPR_CONFIG_SECTION', getenv('PHPR_CONFIG_SECTION') ?: 'production');

/**
 * Root path.
 */
define('PHPR_ROOT_PATH', realpath(dirname(__FILE__) . '/../'));

/**
 * Core path.
 */
define('PHPR_CORE_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'application');

/**
 * User core path.
 */
define('PHPR_USER_CORE_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'application');

/**
 * Library path.
 */
define('PHPR_LIBRARY_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library');

/**
 * Temporary files path.
 */
define('PHPR_TEMP_PATH', PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);

// Decline static file requests back to PHP built-in webserver
if (PHP_SAPI === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
if (!file_exists('vendor/autoload.php')) {
    throw new RuntimeException(
        'Unable to load Composer autoloader. Run `composer install` first.'
    );
}
require_once 'vendor/autoload.php';

// Initialize legacy Phprojekt for backward compatibility
// This sets up database, config, logging, etc.
require_once PHPR_LIBRARY_PATH . DIRECTORY_SEPARATOR . 'Phprojekt.php';

try {
    // Initialize Phprojekt singleton (for database, config, etc.)
    $phprojekt = Phprojekt::getInstance();
} catch (Exception $e) {
    // If Phprojekt initialization fails, show error
    header('HTTP/1.1 500 Internal Server Error');
    echo '<h1>Application Error</h1>';
    echo '<p>An error occurred during application initialization.</p>';
    if (defined('APPLICATION_ENV') && APPLICATION_ENV === 'development') {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    exit(1);
}

// Load Laminas application configuration
$appConfig = require 'config/application.config.php';

// Run the Laminas MVC application
try {
    $application = Laminas\Mvc\Application::init($appConfig);
    $application->run();
} catch (Exception $e) {
    // Handle application errors
    header('HTTP/1.1 500 Internal Server Error');
    echo '<h1>Application Error</h1>';
    echo '<p>An error occurred while running the application.</p>';
    if (defined('APPLICATION_ENV') && APPLICATION_ENV === 'development') {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    error_log('Application error: ' . $e->getMessage());
    error_log($e->getTraceAsString());
    exit(1);
}
