<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 */

return [
    'db' => [
        'driver' => 'Pdo',
        'dsn'    => 'mysql:dbname=phprojekt;host=localhost;charset=utf8',
    ],
    'service_manager' => [
        'factories' => [
            Laminas\Db\Adapter\AdapterInterface::class => Laminas\Db\Adapter\AdapterServiceFactory::class,
        ],
    ],
];
