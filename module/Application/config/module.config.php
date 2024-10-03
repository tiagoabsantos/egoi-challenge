<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Db\Adapter\Adapter;

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
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'users' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/users[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action' => 'index',
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                ],
            ],
            'sms' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/send',
                    'defaults' => [
                        'controller' => Controller\SmsController::class,
                        'action' => 'index',
                    ]
                ],
            ],
            'sms_activemq' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/send_active_mq',
                    'defaults' => [
                        'controller' => Controller\ActiveMqController::class,
                        'action' => 'index',
                    ]
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\UserController::class => function ($container) {
                $dbAdapter = $container->get(Adapter::class);
                return new Controller\UserController($dbAdapter);
            },
            Controller\SmsController::class => InvokableFactory::class,
            Controller\ActiveMqController::class => function ($container) {
                $producer = $container->get(Service\ActiveMqProducer::class);
                return new Controller\ActiveMqController($producer);
            },
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ],
    'service_manager' => [
        'factories' => [
            Adapter::class => function ($container) {
                $config = $container->get('config');
                return new Adapter($config['db']);
            },
            Service\ActiveMqProducer::class => function ($container) {
                return new Service\ActiveMqProducer('tcp://localhost:61613');
            },
            Service\ActiveMqConsumer::class => function ($container) {
                return new Service\ActiveMqConsumer('tcp://localhost:61613');
            },
        ],
    ],
];
