<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Contracts;

/**
 * Plugin context interface.
 *
 * Framework packages depend on this interface, not concrete implementations.
 * Each plugin provides its own PluginContext class implementing this interface.
 * This pattern survives PHP-Scoper namespace rewriting.
 *
 * @package WPZylos\Framework\Core
 */
interface ContextInterface
{
    /**
     * Get the plugin slug.
     *
     * @return string Plugin slug (e.g., 'my-plugin')
     */
    public function slug(): string;

    /**
     * Get the plugin prefix.
     *
     * @return string Plugin prefix (e.g., 'myplugin_')
     */
    public function prefix(): string;

    /**
     * Get the text domain for translations.
     *
     * @return string Text domain (e.g., 'my-plugin')
     */
    public function textDomain(): string;

    /**
     * Get the plugin version.
     *
     * @return string Semantic version (e.g., '1.0.0')
     */
    public function version(): string;

    /**
     * Get the main plugin file path.
     *
     * @return string Absolute path to the main plugin file
     */
    public function file(): string;

    /**
     * Get an absolute path within the plugin directory.
     *
     * @param string $relativePath Relative path from plugin root
     *
     * @return string Absolute path
     */
    public function path(string $relativePath = ''): string;

    /**
     * Get a URL within the plugin directory.
     *
     * @param string $relativePath Relative path from plugin root
     *
     * @return string Full URL
     */
    public function url(string $relativePath = ''): string;

    /**
     * Get a prefixed custom hook name.
     *
     * @param string $name Hook name without a prefix
     *
     * @return string Prefixed hook name (e.g., 'myplugin_after_save')
     */
    public function hook(string $name): string;

    /**
     * Get a prefixed option key.
     *
     * @param string $key Option key without a prefix
     *
     * @return string Prefixed option key
     */
    public function optionKey(string $key): string;

    /**
     * Get a prefixed transient key.
     *
     * @param string $key Transient key without a prefix
     *
     * @return string Prefixed transient key
     */
    public function transientKey(string $key): string;

    /**
     * Get a prefixed cron hook name.
     *
     * @param string $name Cron hook name without a prefix
     *
     * @return string Prefixed cron hook name
     */
    public function cronHook(string $name): string;

    /**
     * Get a prefixed database table name.
     *
     * @param string $name Table name without a prefix
     * @param string $scope 'site' for site-level, 'network' for network-level (multisite)
     *
     * @return string Full table name with WP prefix
     */
    public function tableName(string $name, string $scope = 'site'): string;

    /**
     * Get a prefixed meta-key.
     *
     * @param string $key Meta-key without a prefix
     *
     * @return string Prefixed meta key (e.g., '_myplugin_order_id')
     */
    public function metaKey(string $key): string;

    /**
     * Get a prefixed asset handle.
     *
     * @param string $handle Asset handle without prefix
     *
     * @return string Prefixed asset handle
     */
    public function assetHandle(string $handle): string;
}
