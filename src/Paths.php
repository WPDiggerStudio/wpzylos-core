<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core;

use WPZylos\Framework\Core\Contracts\ContextInterface;

/**
 * Path and URL resolution utilities.
 *
 * Provides convenient methods for resolving paths and URLs
 * relative to the plugin directory.
 *
 * @package WPZylos\Framework\Core
 */
class Paths
{
    /**
     * @var ContextInterface Plugin context
     */
    private ContextInterface $context;

    /**
     * @var array<string, string> Path aliases
     */
    private array $aliases = [];

    /**
     * Create paths instance.
     *
     * @param ContextInterface $context Plugin context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
        $this->registerDefaultAliases();
    }

    /**
     * Register default path aliases.
     *
     * @return void
     */
    private function registerDefaultAliases(): void
    {
        $this->aliases = [
            'app'        => 'app',
            'config'     => 'config',
            'routes'     => 'routes',
            'resources'  => 'resources',
            'views'      => 'resources/views',
            'lang'       => 'resources/lang',
            'assets'     => 'resources/assets',
            'database'   => 'database',
            'migrations' => 'database/migrations',
            'storage'    => 'storage',
            'logs'       => 'storage/logs',
            'cache'      => 'storage/cache',
        ];
    }

    /**
     * Register a custom path alias.
     *
     * @param string $alias Alias name
     * @param string $path Relative path from plugin root
     *
     * @return static
     */
    public function alias(string $alias, string $path): static
    {
        $this->aliases[ $alias ] = $path;

        return $this;
    }

    /**
     * Get an absolute path.
     *
     * Supports alias resolution (e.g., '@views/welcome.php').
     *
     * @param string $path Path or alias
     *
     * @return string Absolute path
     */
    public function path(string $path = ''): string
    {
        $resolved = $this->resolveAlias($path);

        return $this->context->path($resolved);
    }

    /**
     * Get a URL.
     *
     * Supports alias resolution (e.g., '@assets/css/app.css').
     *
     * @param string $path Path or alias
     *
     * @return string Full URL
     */
    public function url(string $path = ''): string
    {
        $resolved = $this->resolveAlias($path);

        return $this->context->url($resolved);
    }

    /**
     * Resolve a path alias.
     *
     * @param string $path Path that may contain alias prefix (@alias)
     *
     * @return string Resolved relative path
     */
    private function resolveAlias(string $path): string
    {
        if (str_starts_with($path, '@')) {
            $parts = explode('/', $path, 2);
            $alias = substr($parts[0], 1);

            if (isset($this->aliases[ $alias ])) {
                $basePath  = $this->aliases[ $alias ];
                $remainder = $parts[1] ?? '';

                return $remainder !== ''
                    ? $basePath . '/' . $remainder
                    : $basePath;
            }
        }

        return $path;
    }

    /**
     * Check if a path exists.
     *
     * @param string $path Path or alias
     *
     * @return bool True if a path exists
     */
    public function exists(string $path): bool
    {
        return file_exists($this->path($path));
    }

    /**
     * Get WordPress upload directory for the plugin.
     *
     * Creates the directory if it doesn't exist.
     *
     * @param string $subPath Optional subdirectory
     *
     * @return string Absolute path to upload directory
     */
    public function uploads(string $subPath = ''): string
    {
        $uploadDir     = wp_upload_dir();
        $pluginUploads = $uploadDir['basedir'] . '/' . $this->context->slug();

        if (! is_dir($pluginUploads)) {
            wp_mkdir_p($pluginUploads);
        }

        if ($subPath !== '') {
            $fullPath = $pluginUploads . '/' . ltrim($subPath, '/');
            $dir      = dirname($fullPath);

            if (! is_dir($dir)) {
                wp_mkdir_p($dir);
            }

            return $fullPath;
        }

        return $pluginUploads;
    }

    /**
     * Get all registered aliases.
     *
     * @return array<string, string> Alias => path mapping
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }
}
