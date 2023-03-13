<?php

namespace Southaxis\RegiCare\Container;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Southaxis\RegiCare\ActivityShow;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;
use function Southaxis\Helpers\service;

/**
 * @internal
 *
 * @coversNothing
 */
class PluginContainerTest extends TestCase
{
    public function testGetInstance(): void
    {
        assertInstanceOf(ContainerInterface::class, PluginContainer::getInstance());
    }

    /**
     * @dataProvider containerAccessorProvider
     *
     * @depends testGetInstance
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGet($accessor): void
    {
        assertInstanceOf(ActivityShow::class, $accessor(ActivityShow::class));
    }

    /**
     * @depends testGetInstance
     */
    public function testHas(): void
    {
        assertTrue(PluginContainer::getInstance()->has(ActivityShow::class));
        assertFalse(PluginContainer::getInstance()->has('non-existent-binding'));
    }

    protected function containerAccessorProvider(): array
    {
        return [
            '`service` helper function'  => [service(...)],
            '`PluginContainer` instance' => [PluginContainer::getInstance()->get(...)],
        ];
    }
}
