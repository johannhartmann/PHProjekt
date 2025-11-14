<?php
/**
 * Core Module Configuration - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

namespace Application\Core;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\TabController::class => InvokableFactory::class,
            Controller\RoleController::class => InvokableFactory::class,
            Controller\HistoryController::class => InvokableFactory::class,
            Controller\SettingController::class => InvokableFactory::class,
            Controller\ModuleController::class => InvokableFactory::class,
            Controller\UserController::class => InvokableFactory::class,
            Controller\AdministrationController::class => InvokableFactory::class,
            Controller\UpgradeController::class => InvokableFactory::class,
            Controller\ModuleDesignerController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'Core\Index' => Controller\IndexController::class,
            'Core\Tab' => Controller\TabController::class,
            'Core\Role' => Controller\RoleController::class,
            'Core\History' => Controller\HistoryController::class,
            'Core\Setting' => Controller\SettingController::class,
            'Core\Module' => Controller\ModuleController::class,
            'Core\User' => Controller\UserController::class,
            'Core\Administration' => Controller\AdministrationController::class,
            'Core\Upgrade' => Controller\UpgradeController::class,
            'Core\ModuleDesigner' => Controller\ModuleDesignerController::class,
        ],
    ],
];
