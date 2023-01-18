<?php

declare(strict_types=1);

/**
 * Plugin Name: RegiCare
 * Description: Adsysco RegiCare API plugin
 * Version: 4.0.4
 * Author: SouthAxis
 * Author URI: https://www.southaxis.com/
 * Text Domain: sb.
 */

use Southaxis\RegiCare\Container\PluginContainer;

defined('ABSPATH') || exit('Forbidden');

if (! defined('REGICARE_PLUGIN_URL')) {
    define('REGICARE_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (! file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new RuntimeException('Could not find composers autoload.php file.');
}

include_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/inc/functions.php';

require_once __DIR__ . '/inc/settings.php';

require_once __DIR__ . '/inc/shortcodes.php';

add_action('init', function (): void {
    PluginContainer::getInstance();
});

add_action('wp_enqueue_scripts', function (): void {
    $directory = REGICARE_PLUGIN_URL;

    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_style('e2b-admin-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css', false, media: false);

    wp_enqueue_style('mytheme-custom', $directory . '/assets/css/regicare.css');
    wp_enqueue_script('mytheme-jquery-script', 'https://code.jquery.com/jquery-3.4.1.min.js', null, true);

    wp_enqueue_script('mytheme-script', $directory . '/assets/js/custom/script.js', ['jquery-ui-core', 'jquery-ui-datepicker'], true, true);
    wp_localize_script('mytheme-script', 'plugin', ['ajax_url' => admin_url('admin-ajax.php')]);
}, 11);

/**
 * Adds account navigation item to the navigation.
 *
 * @param mixed $items
 */
add_filter('wp_nav_menu_main-menu_items', function ($items): string {
    global $wp;

    $link     = home_url($wp->request);
    $userName = $_SESSION['user']['naam'] ?? null;

    if (isset($_SESSION['user'])) {
        $items .= '<li class="menu-item menu-item-has-children">
                <a href="#" class="menu-link elementor-item">' . substr($userName, 0, 10) . '</a>
                <ul class="sub-menu elementor-nav-menu--dropdown sm-nowrap">
                    <li class="menu-item">
                        <a class="menu-link elementor-sub-item" href="' . $link . '/account">Mijn account</a>
                    </li>
                    <li class="menu-item">
                        <a class="menu-link elementor-sub-item" href="' . $link . '/?logout=true">Afmelden</a>
                    </li>
                </ul>
            </li>';
    } else {
        $items .= '<li class="menu-item"><a class="menu-link elementor-item" href="' . $link . '/login">Inloggen</a></li>';
    }

    return $items;
}, 20, 1);

