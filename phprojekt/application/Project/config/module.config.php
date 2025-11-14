<?php
/**
 * Project Module Configuration - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

namespace Application\Project;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\ProjectController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'Project\Index' => Controller\IndexController::class,
            'Project\Project' => Controller\ProjectController::class,
        ],
    ],
];
