<?php

namespace Southaxis\RegiCare\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Southaxis\RegiCare\ActivityShow;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;

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
     * @depends testGetInstance
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGet(): void
    {
        assertInstanceOf(
            ActivityShow::class,
            PluginContainer::getInstance()->get(ActivityShow::class)
        );
    }

    /**
     * @depends testGetInstance
     */
    public function testHas(): void
    {
        assertTrue(PluginContainer::getInstance()->has(ActivityShow::class));
        assertFalse(PluginContainer::getInstance()->has('non-existant-binding'));
    }
}
