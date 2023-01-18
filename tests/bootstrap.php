<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->load();

if (! isset($_ENV['INCLUDE_WP'])) {
    return;
}

if (! defined('ABSPATH')) {
    define('ABSPATH', $_ENV['TEST_ABSPATH']);
}

$_SERVER['HTTPS']          = 'on';
$_SERVER['HTTP_HOST']      = $_ENV['TEST_HOST'];
$_SERVER['REQUEST_URI']    = '';
$_SERVER['REQUEST_METHOD'] = 'POST';

ob_start();

require_once ABSPATH . 'index.php';

require_once ABSPATH . 'wp-config.php';

require_once ABSPATH . 'wp-includes/functions.php';
ob_get_clean();

require_once dirname(__DIR__) . '/wp-regicare.php';

