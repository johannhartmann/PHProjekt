<?php
/**
 * Test Bootstrap for Laminas Migration
 */

// Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_runtime", 0);
ini_set("magic_quotes_sybase", 0);

$config = "configuration.php";
if (getenv('P6_TEST_CONFIG')) {
    $config = getenv('P6_TEST_CONFIG');
}

/* use command line switches to overwrite this */
define("DEFAULT_CONFIG_FILE", $config);
define("PHPR_CONFIG_FILE", $config);
define("DEFAULT_CONFIG_SECTION", "testing-mysql");
define("PHPR_CONFIG_SECTION", "testing-mysql");

define('PHPR_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));

// Load Laminas application
$appConfig = require __DIR__ . '/../../config/application.config.php';

// Initialize Laminas application for testing
$application = Laminas\Mvc\Application::init($appConfig);
$serviceManager = $application->getServiceManager();

// Make service manager globally available for tests
$GLOBALS['serviceManager'] = $serviceManager;

// Set up database connection
$dbAdapter = $serviceManager->get(Laminas\Db\Adapter\AdapterInterface::class);

// Legacy compatibility - still needed for old test code
include_once 'DatabaseTest.php';

// Initialize legacy Phprojekt if needed
if (file_exists(PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Phprojekt.php')) {
    require_once PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Phprojekt.php';
    if (class_exists('Phprojekt')) {
        try {
            Phprojekt::getInstance();
            restore_error_handler();
        } catch (\Exception $e) {
            // Silently ignore if Phprojekt::getInstance() fails during migration
            error_log("Warning: Phprojekt::getInstance() failed: " . $e->getMessage());
        }
    }
}

// Set SQL mode for MySQL
try {
    $dbAdapter->query('SET sql_mode="STRICT_ALL_TABLES"');
} catch (\Exception $e) {
    error_log("Warning: Could not set SQL mode: " . $e->getMessage());
}

// Set up session for authentication (if using Laminas Session)
if (class_exists('Laminas\Session\Container')) {
    $authContainer = new Laminas\Session\Container('Phprojekt_Auth-login');
    $authContainer->userId = 1;
    $authContainer->admin = 1;
}
