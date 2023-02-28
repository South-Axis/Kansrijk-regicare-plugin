<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Southaxis\RegiCare\Activiteiten;
use Southaxis\RegiCare\ActivityShow;
use Southaxis\RegiCare\Auth;
use Southaxis\RegiCare\Authentication;
use Southaxis\RegiCare\Client\Config;
use Southaxis\RegiCare\Container\PluginContainer;
use Southaxis\RegiCare\Vacatures;
use function Southaxis\Helpers\service;

/**
 * Container factory-bindings.
 *
 * @see PluginContainer
 * @see service()
 *
 * @link h
 */
return [
    'token'               => static fn () => get_option('regicare_key', ''),
    'domain'              => static fn () => get_option('regicare_domain', ''),
    ActivityShow::class   => static fn () => new ActivityShow(),
    Authentication::class => static fn () => new Authentication(),
    Config::class         => static fn (ContainerInterface $c) => new Config($c->get('domain'), $c->get('token')),
    Vacatures::class      => static fn (ContainerInterface $c) => new Vacatures($c->get(Config::class)),
    Activiteiten::class   => static fn (ContainerInterface $c) => new Activiteiten($c->get(Config::class)),
    Auth::class           => static fn (ContainerInterface $c) => new Auth($c->get(Config::class)),
];
