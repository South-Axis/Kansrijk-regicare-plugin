<?php

declare(strict_types=1);

defined('ABSPATH') || exit('Forbidden');

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Southaxis\Helpers\getActivityShow;
use function Southaxis\Helpers\getAuthentication;

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function regicare_actvities_short(): bool|string
{
    ob_start();

    include dirname(__DIR__) . '/assets/views/activity-filter.php';

    return ob_get_clean();
}

add_shortcode('regicareActiviteiten', 'regicare_actvities_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function login_short(): bool|string
{
    return getAuthentication()->login_regicare_show();
}

add_shortcode('login', 'login_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function register_short(): bool|string
{
    return getAuthentication()->register_regicare_show();
}

add_shortcode('register', 'register_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function register_child_short(): bool|string
{
    return getAuthentication()->register_child_regicare_show();
}

add_shortcode('registerChild', 'register_child_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function other_child_short(): bool|string
{
    return getAuthentication()->other_child_short_show();
}

add_shortcode('otherChild', 'other_child_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function account_short(): bool|string
{
    return getAuthentication()->account_short_show();
}

add_shortcode('account', 'account_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function account_update_short(): bool|string
{
    return getAuthentication()->account_update_short_show();
}

add_shortcode('account_update', 'account_update_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function account_child_short(): void
{
    getAuthentication()->account_child_short_show();
}

add_shortcode('account_child', 'account_child_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function account_child_update_short(): bool|string
{
    return getAuthentication()->account_child_update_short_show();
}

add_shortcode('account_child_update', 'account_child_update_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function register_activities_short(): bool|string|null
{
    return getActivityShow()->registerActivity();
}

add_shortcode('registerActivity', 'register_activities_short');

/**
 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
 */
function forgot_password_short(): bool|string
{
    return getAuthentication()->forgot_password_show();
}

add_shortcode('forgot-password', 'forgot_password_short');
