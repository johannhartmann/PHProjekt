<?php
/**
 * Calendar2 Module Configuration - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2010 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

namespace Application\Calendar2;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\Calendar2Controller::class => InvokableFactory::class,
            Controller\CaldavController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'Calendar2\Index' => Controller\IndexController::class,
            'Calendar2\Calendar2' => Controller\Calendar2Controller::class,
            'Calendar2\Caldav' => Controller\CaldavController::class,
        ],
    ],
];
