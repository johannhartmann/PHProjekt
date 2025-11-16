<?php
/**
 * Module configuration for Default module
 */
namespace Application\Default;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'app' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/app[/:path]',
                    'constraints' => [
                        'path' => '.*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ReactController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'react' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/react[/:path]',
                    'constraints' => [
                        'path' => '.*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ReactController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'default' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/[:controller[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Default\Controller',
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
            Controller\LoginController::class => InvokableFactory::class,
            Controller\SearchController::class => InvokableFactory::class,
            Controller\TagController::class => InvokableFactory::class,
            Controller\JsController::class => InvokableFactory::class,
            Controller\ErrorController::class => InvokableFactory::class,
            Controller\ReactController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'Index' => Controller\IndexController::class,
            'Login' => Controller\LoginController::class,
            'Search' => Controller\SearchController::class,
            'Tag' => Controller\TagController::class,
            'Js' => Controller\JsController::class,
            'Error' => Controller\ErrorController::class,
            'React' => Controller\ReactController::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'                                 => __DIR__ . '/../Views/layout/layout.phtml',
            'application/default/index/index'               => __DIR__ . '/../Views/index/index.phtml',
            'application-default-controller-index/index'    => __DIR__ . '/../Views/index/index.phtml',
            'application/default/react/index'               => __DIR__ . '/../Views/react/index.phtml',
            'error/404'                                     => __DIR__ . '/../Views/error/404.phtml',
            'error/index'                                   => __DIR__ . '/../Views/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../Views',
        ],
    ],
];
