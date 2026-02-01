<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Cache;

use WPZylos\Framework\Core\Contracts\ContextInterface;

/**
 * Cache repository using WordPress object cache and transients.
 *
 * Provides context-prefixed caching with fallback to transients.
 *
 * @package WPZylos\Framework\Core\Cache
 */
class CacheRepository
{
    /**
     * @var ContextInterface Plugin context
     */
    private ContextInterface $context;

    /**
     * @var string Cache group
     */
    private string $group;

    /**
     * Create a cache repository.
     *
     * @param ContextInterface $context Plugin context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
        $this->group   = $context->slug();
    }

    /**
     * Get item from the cache.
     *
     * @param string $key Cache key
     * @param mixed $default Default value if not found
     *
     * @return mixed Cached value or default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $prefixed = $this->prefixKey($key);
        $value    = wp_cache_get($prefixed, $this->group);

        if ($value === false) {
            return $default;
        }

        return $value;
    }

    /**
     * Store item in cache.
     *
     * @param string $key Cache key
     * @param mixed $value Value to store
     * @param int $ttl Time to live in seconds (0 = no expiry)
     *
     * @return bool True if stored
     */
    public function put(string $key, mixed $value, int $ttl = 3600): bool
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_set($prefixed, $value, $this->group, $ttl);
    }

    /**
     * Store item in a cache if not exists.
     *
     * @param string $key Cache key
     * @param mixed $value Value to store
     * @param int $ttl Time to live in seconds
     *
     * @return bool True if stored (was not present)
     */
    public function add(string $key, mixed $value, int $ttl = 3600): bool
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_add($prefixed, $value, $this->group, $ttl);
    }

    /**
     * Remove item from the cache.
     *
     * @param string $key Cache key
     *
     * @return bool True if removed
     */
    public function forget(string $key): bool
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_delete($prefixed, $this->group);
    }

    /**
     * Check if item exists in cache.
     *
     * @param string $key Cache key
     *
     * @return bool True if exists
     */
    public function has(string $key): bool
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_get($prefixed, $this->group) !== false;
    }

    /**
     * Get item or store result of callback.
     *
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Value generator
     *
     * @return mixed Cached or generated value
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);

        return $value;
    }

    /**
     * Get an item or store forever.
     *
     * @param string $key Cache key
     * @param callable $callback Value generator
     *
     * @return mixed Cached or generated value
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        return $this->remember($key, 0, $callback);
    }

    /**
     * Increment a numeric value.
     *
     * @param string $key Cache key
     * @param int $amount Amount to increment
     *
     * @return int|false New value or false on failure
     */
    public function increment(string $key, int $amount = 1): int|false
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_incr($prefixed, $amount, $this->group);
    }

    /**
     * Decrement a numeric value.
     *
     * @param string $key Cache key
     * @param int $amount Amount to decrement
     *
     * @return int|false New value or false on failure
     */
    public function decrement(string $key, int $amount = 1): int|false
    {
        $prefixed = $this->prefixKey($key);

        return wp_cache_decr($prefixed, $amount, $this->group);
    }

    /**
     * Flush all items in this cache group.
     *
     * Note: Only works with object cache backends that support groups.
     *
     * @return bool True if flushed
     */
    public function flush(): bool
    {
        if (function_exists('wp_cache_flush_group')) {
            return wp_cache_flush_group($this->group);
        }

        // Fallback: can't flush without group support
        return false;
    }

    // =========================================================================
    // Transient Methods (Persistent Fallback)
    // =========================================================================

    /**
     * Get transient value.
     *
     * @param string $key Transient key
     * @param mixed $default Default value
     *
     * @return mixed Transient value or default
     */
    public function transientGet(string $key, mixed $default = null): mixed
    {
        $prefixed = $this->context->transientKey($key);
        $value    = get_transient($prefixed);

        return $value !== false ? $value : $default;
    }

    /**
     * Store transient value.
     *
     * @param string $key Transient key
     * @param mixed $value Value to store
     * @param int $ttl Time to live in seconds
     *
     * @return bool True if stored
     */
    public function transientPut(string $key, mixed $value, int $ttl = 3600): bool
    {
        $prefixed = $this->context->transientKey($key);

        return set_transient($prefixed, $value, $ttl);
    }

    /**
     * Delete transient.
     *
     * @param string $key Transient key
     *
     * @return bool True if deleted
     */
    public function transientForget(string $key): bool
    {
        $prefixed = $this->context->transientKey($key);

        return delete_transient($prefixed);
    }

    /**
     * Remember using transients.
     *
     * @param string $key Transient key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Value generator
     *
     * @return mixed Cached or generated value
     */
    public function transientRemember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->transientGet($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->transientPut($key, $value, $ttl);

        return $value;
    }

    /**
     * Prefix the cache key with context.
     *
     * @param string $key Original key
     *
     * @return string Prefixed key
     */
    private function prefixKey(string $key): string
    {
        return $this->context->prefix() . $key;
    }
}
