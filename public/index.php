<?php
/**
 * Laminas MVC Application Entry Point
 */

declare(strict_types=1);

// Composer autoloading
include __DIR__ . '/../phprojekt/vendor/autoload.php';

if (! class_exists(Laminas\Mvc\Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../phprojekt/config/application.config.php';

// Run the application!
Laminas\Mvc\Application::init($appConfig)->run();
