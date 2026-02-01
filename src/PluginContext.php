<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core;

use InvalidArgumentException;
use WPZylos\Framework\Core\Contracts\ContextInterface;

/**
 * Plugin context implementation.
 *
 * Holds all plugin identity information. This is the single source of truth
 * for slug, prefix, text domain, paths, and URLs.
 *
 * Note: In production plugins, this class should be copied into the plugin's
 * own namespace to survive PHP-Scoper. The framework depends on ContextInterface.
 *
 * @package WPZylos\Framework\Core
 */
class PluginContext implements ContextInterface
{
    /**
     * @var string Main plugin file path
     */
    private string $file;

    /**
     * @var string Plugin slug
     */
    private string $slug;

    /**
     * @var string Plugin prefix for options, hooks, etc.
     */
    private string $prefix;

    /**
     * @var string Text domain for translations
     */
    private string $textDomain;

    /**
     * @var string Plugin version
     */
    private string $version;

    /**
     * @var string|null Cached plugin directory path
     */
    private ?string $basePath = null;

    /**
     * @var string|null Cached plugin directory URL
     */
    private ?string $baseUrl = null;

    /**
     * Create a new plugin context.
     *
     * @param array{
     *     file: string,
     *     slug: string,
     *     prefix: string,
     *     textDomain: string,
     *     version: string
     * } $config Configuration array
     */
    private function __construct(array $config)
    {
        $this->file       = $config['file'];
        $this->slug       = $config['slug'];
        $this->prefix     = $config['prefix'];
        $this->textDomain = $config['textDomain'];
        $this->version    = $config['version'];
    }

    /**
     * Create a plugin context from configuration.
     *
     * @param array{
     *     file: string,
     *     slug: string,
     *     prefix: string,
     *     textDomain: string,
     *     version: string
     * } $config Configuration array
     *
     * @return static
     */
    public static function create(array $config): static
    {
        self::validateConfig($config);

        return new static($config);
    }

    /**
     * Validate configuration array.
     *
     * @param array<string, mixed> $config Configuration to validate
     *
     * @return void
     * @throws InvalidArgumentException If required keys are missing
     */
    private static function validateConfig(array $config): void
    {
        $required = [ 'file', 'slug', 'prefix', 'textDomain', 'version' ];
        $missing  = array_diff($required, array_keys($config));

        if (! empty($missing)) {
            throw new InvalidArgumentException(
                sprintf('Missing required config keys: %s', implode(', ', $missing))
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function slug(): string
    {
        return $this->slug;
    }

    /**
     * {@inheritDoc}
     */
    public function prefix(): string
    {
        return $this->prefix;
    }

    /**
     * {@inheritDoc}
     */
    public function textDomain(): string
    {
        return $this->textDomain;
    }

    /**
     * {@inheritDoc}
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * {@inheritDoc}
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * {@inheritDoc}
     */
    public function path(string $relativePath = ''): string
    {
        if ($this->basePath === null) {
            $this->basePath = plugin_dir_path($this->file);
        }

        if ($relativePath === '') {
            return $this->basePath;
        }

        return $this->basePath . ltrim($relativePath, '/\\');
    }

    /**
     * {@inheritDoc}
     */
    public function url(string $relativePath = ''): string
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = plugin_dir_url($this->file);
        }

        if ($relativePath === '') {
            return $this->baseUrl;
        }

        return $this->baseUrl . ltrim($relativePath, '/');
    }

    /**
     * {@inheritDoc}
     */
    public function hook(string $name): string
    {
        return $this->prefix . $name;
    }

    /**
     * {@inheritDoc}
     */
    public function optionKey(string $key): string
    {
        return $this->prefix . $key;
    }

    /**
     * {@inheritDoc}
     */
    public function transientKey(string $key): string
    {
        return $this->prefix . $key;
    }

    /**
     * {@inheritDoc}
     */
    public function cronHook(string $name): string
    {
        return $this->prefix . $name;
    }

    /**
     * {@inheritDoc}
     */
    public function tableName(string $name, string $scope = 'site'): string
    {
        global $wpdb;

        $wpPrefix = ( $scope === 'network' && isset($wpdb->base_prefix) )
            ? $wpdb->base_prefix
            : $wpdb->prefix;

        return $wpPrefix . $this->prefix . $name;
    }

    /**
     * {@inheritDoc}
     */
    public function metaKey(string $key): string
    {
        return '_' . $this->prefix . $key;
    }

    /**
     * {@inheritDoc}
     */
    public function assetHandle(string $handle): string
    {
        return $this->slug . '-' . $handle;
    }
}
