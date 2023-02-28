<?php

declare(strict_types=1);

namespace Southaxis\Helpers;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Generator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Southaxis\RegiCare\ActivityShow;
use Southaxis\RegiCare\Authentication;
use Southaxis\RegiCare\Container\PluginContainer;
use function iterator_to_array;

/**
 * Retrieve an instance from the service container.
 *
 * This way each instance will only be created the first time it is called,
 * for each following call, the same instance will be returned.
 *
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 *
 * @see PluginContainer
 * @see Container
 * @see ContainerInterface
 *
 * @noinspection DebugFunctionUsageInspection
 */
function service(string $id): mixed
{
    try {
        return PluginContainer::getInstance()->get($id);
    } catch (DependencyException|NotFoundException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        if (\defined('ABSPATH')) {
            /** @noinspection ForgottenDebugOutputInspection */
            error_log($e->getMessage());

            return false;
        }

        throw $e;
    }
}

/**
 * Wrapper function to get the ActivityShow service.
 *
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 *
 * @see ActivityShow
 */
function getActivityShow(): ActivityShow
{
    return service(ActivityShow::class);
}

/**
 * Wrapper function to get the Authentication service.
 *
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 *
 * @see Authentication
 */
function getAuthentication(): Authentication
{
    return service(Authentication::class);
}

function mapRegicareFilters(array $input, array $filters = ['dag', 'groepering', 'vrijkenmerk06', 'locatie']): array
{
    return iterator_to_array((function () use ($filters, $input): Generator {
        foreach ($filters as $filterName) {
            if (isset($input["{$filterName}ID"], $input["{$filterName}Text"]) && (int)$input["{$filterName}ID"] !== 0) {
                yield $filterName => [(int)$input["{$filterName}ID"]];
            }
        }
    })());
}
