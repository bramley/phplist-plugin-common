<?php

namespace phpList\plugin\Common;

use Psr\Container\ContainerInterface;

/*
 * This file provides the dependencies for use by the dependency injection container.
 */

return [
    'phpList\plugin\Common\Context' => function (ContainerInterface $container) {
        return Context::create();
    },
    'phpList\plugin\Common\DAO\Attribute' => function (ContainerInterface $container) {
        return new \phpList\plugin\Common\DAO\Attribute(
            $container->get('phpList\plugin\Common\DB')
        );
    },
    'phpList\plugin\Common\DAO\Config' => function (ContainerInterface $container) {
        return new \phpList\plugin\Common\DAO\Config(
            $container->get('phpList\plugin\Common\DB')
        );
    },
    'phpList\plugin\Common\DAO\Lists' => function (ContainerInterface $container) {
        return new \phpList\plugin\Common\DAO\Lists(
            $container->get('phpList\plugin\Common\DB')
        );
    },
    'phpList\plugin\Common\DAO\Message' => function (ContainerInterface $container) {
        return new \phpList\plugin\Common\DAO\Message(
            $container->get('phpList\plugin\Common\DB')
        );
    },
    'phpList\plugin\Common\DAO\User' => function (ContainerInterface $container) {
        return new \phpList\plugin\Common\DAO\User(
            $container->get('phpList\plugin\Common\DB')
        );
    },
    'phpList\plugin\Common\DB' => function (ContainerInterface $container) {
        return new DB();
    },
    'Logger' => function (ContainerInterface $container) {
        return Logger::instance();
    },
];
