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
        return (static fn () => include \dirname(__FILE__, 3) . '/config.php')();
    }
}
