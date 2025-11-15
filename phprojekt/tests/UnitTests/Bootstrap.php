<?php
/**
 * PHPUnit Test Bootstrap for Laminas MVC
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

include_once 'DatabaseTest.php';
include_once 'FrontInit.php';

// Load Laminas application configuration
$appConfig = require PHPR_ROOT_PATH . '/config/application.config.php';

// Initialize Laminas MVC Application
$application = Laminas\Mvc\Application::init($appConfig);
$serviceManager = $application->getServiceManager();

// Make service manager globally available for tests
$GLOBALS['serviceManager'] = $serviceManager;

// Initialize legacy Phprojekt for database/config
if (file_exists(PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Phprojekt.php')) {
    require_once PHPR_ROOT_PATH . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'Phprojekt.php';
    if (class_exists('Phprojekt')) {
        Phprojekt::getInstance();
        // Phprojekt::getInstance() indirectly sets the error handler which eats our errors.
        restore_error_handler();
    }
}

// Set up authentication session with Laminas Session
if (class_exists('Laminas\Session\Container')) {
    $authContainer = new Laminas\Session\Container('Phprojekt_Auth-login');
    $authContainer->userId = 1;
    $authContainer->admin = 1;
}

// Set SQL mode for MySQL
try {
    if (class_exists('Phprojekt') && Phprojekt::getInstance() && Phprojekt::getInstance()->getDb()) {
        Phprojekt::getInstance()->getDb()->query('SET sql_mode="STRICT_ALL_TABLES"');
    } elseif (isset($serviceManager)) {
        $dbAdapter = $serviceManager->get(Laminas\Db\Adapter\AdapterInterface::class);
        $dbAdapter->query('SET sql_mode="STRICT_ALL_TABLES"');
    }
} catch (Exception $e) {
    error_log("Warning: Could not set SQL mode: " . $e->getMessage());
}
