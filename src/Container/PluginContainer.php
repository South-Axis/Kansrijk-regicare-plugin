<?php

declare(strict_types=1);

namespace Southaxis\RegiCare\Container;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Southaxis\RegiCare\Activiteiten;
use Southaxis\RegiCare\ActivityShow;
use Southaxis\RegiCare\Auth;
use Southaxis\RegiCare\Authentication;
use Southaxis\RegiCare\Client\Config;
use Southaxis\RegiCare\Vacatures;

class PluginContainer implements ContainerInterface
{
    private static ?self $instance = null;

    private ContainerInterface $container;

    public static function getInstance(): self
    {
        return self::$instance
               ?? self::$instance = new self();
    }

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $this->container = (new ContainerBuilder())
            ->useAnnotations(false)
            ->useAutowiring(true)
            ->addDefinitions($this->getDefinitions())
            ->build();
    }

    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    private function getDefinitions(): array
    {
        return [
            'token'               => static function () {
                return get_option('regicare_key', '');
            },
            'domain'              => static function () {
                return get_option('regicare_domain', '');
            },
            ActivityShow::class   => static fn () => new ActivityShow(),
            Authentication::class => static fn () => new Authentication(),
            Config::class         => static fn (ContainerInterface $c) => new Config($c->get('domain'), $c->get('token')),
            Vacatures::class      => static fn (ContainerInterface $c) => new Vacatures($c->get(Config::class)),
            Activiteiten::class   => static fn (ContainerInterface $c) => new Activiteiten($c->get(Config::class)),
            Auth::class           => static fn (ContainerInterface $c) => new Auth($c->get(Config::class)),
        ];
    }
}
