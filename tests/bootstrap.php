<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap file.
 *
 * Mocks WordPress functions for unit testing.
 */

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Mock ABSPATH
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

// Mock WordPress functions used by the package

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path(string $file): string
    {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url(string $file): string
    {
        return 'https://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugins_url')) {
    function plugins_url(string $path = '', string $plugin = ''): string
    {
        $base = 'https://example.com/wp-content/plugins/';
        if ($plugin) {
            $base .= basename(dirname($plugin)) . '/';
        }
        return $base . ltrim($path, '/');
    }
}
