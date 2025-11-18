<?php
/**
 * Timecard Module Configuration - Laminas MVC
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License version 3 as published by the Free Software Foundation
 *
 * @copyright  Copyright (c) 2012 Mayflower GmbH (http://www.mayflower.de)
 * @license    LGPL v3 (See LICENSE file)
 */

namespace Application\Timecard;

use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'timecard' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/Timecard[/:controller[/:action]]',
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Timecard\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\TimecardController::class => InvokableFactory::class,
            Controller\CaldavController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'Timecard\Index' => Controller\IndexController::class,
            'Timecard\Timecard' => Controller\TimecardController::class,
            'Timecard\Caldav' => Controller\CaldavController::class,
            'Index' => Controller\IndexController::class,
            'Timecard' => Controller\TimecardController::class,
            'Caldav' => Controller\CaldavController::class,
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
