<?php
/**
 * PHPUnit Test Bootstrap for Laminas MVC
 */

// Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_runtime", 0);
ini_set("magic_quotes_sybase", 0);

define('PHPR_ROOT_PATH', realpath(dirname(__FILE__) . '/../../'));

$config = "configuration.php";
if (getenv('P6_TEST_CONFIG')) {
    $config = getenv('P6_TEST_CONFIG');
}

/* use command line switches to overwrite this */
define("DEFAULT_CONFIG_FILE", $config);
define("PHPR_CONFIG_FILE", $config);
define("DEFAULT_CONFIG_SECTION", "testing-mysql");
define("PHPR_CONFIG_SECTION", "testing-mysql");

// Register autoloader for application modules (Project_Models_*, etc.)
spl_autoload_register(function($className) {
    // Convert Project_Models_Project to Project/Models/Project.php
    if (strpos($className, '_') !== false) {
        $parts = explode('_', $className);
        $file = PHPR_ROOT_PATH . '/application/' . implode('/', $parts) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});

include_once __DIR__ . '/DatabaseTest.php';
include_once __DIR__ . '/FrontInit.php';

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
    $authContainer = new Laminas\Session\Container('Phprojekt_Auth_login');
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
