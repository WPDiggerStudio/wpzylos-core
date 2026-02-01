<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Support;

/**
 * Array helper utilities.
 *
 * Common array manipulation methods using dot notation.
 *
 * @package WPZylos\Framework\Core
 */
class Arr
{
    /**
     * Get an item from an array using dot notation.
     *
     * @param array<string|int, mixed> $array Source array
     * @param string|int|null $key Key using dot notation
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public static function get(array $array, string|int|null $key, mixed $default = null): mixed
    {
        if ($key === null) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (!str_contains((string) $key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Set an item in an array using dot notation.
     *
     * @param array<string|int, mixed> $array Source array (modified by reference)
     * @param string|int|null $key Key using dot notation
     * @param mixed $value Value to set
     * @return array<string|int, mixed> Modified array
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if ($key === null) {
            return $array = $value;
        }

        $keys = explode('.', (string) $key);

        foreach ($keys as $i => $segment) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Check if an item exists in an array using dot notation.
     *
     * @param array<string|int, mixed> $array Source array
     * @param string|int $key Key using dot notation
     * @return bool
     */
    public static function has(array $array, string|int $key): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove an item from an array using dot notation.
     *
     * @param array<string|int, mixed> $array Source array (modified by reference)
     * @param string|int|string[]|int[] $keys Key(s) to remove
     * @return void
     */
    public static function forget(array &$array, string|int|array $keys): void
    {
        $original = &$array;

        $keys = (array) $keys;

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
                continue;
            }

            $parts = explode('.', (string) $key);

            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Flatten a multidimensional array into a single level.
     *
     * @param array $array Source array
     * @param int $depth Maximum depth to flatten (INF for unlimited)
     * @return array<int, mixed>
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : self::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Get only specified keys from an array.
     *
     * @param array<string|int, mixed> $array Source array
     * @param string[]|int[] $keys Keys to include
     * @return array<string|int, mixed>
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Get all keys except specified ones from an array.
     *
     * @param array<string|int, mixed> $array Source array
     * @param string[]|int[] $keys Keys to exclude
     * @return array<string|int, mixed>
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Get the first element matching a callback.
     *
     * @param array $array Source array
     * @param callable|null $callback Callback to match (receives value, key)
     * @param mixed $default Default if not found
     * @return mixed
     */
    public static function first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($array)) {
                return $default;
            }

            return reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Wrap value in an array if not already.
     *
     * @param mixed $value Value to wrap
     * @return array
     */
    public static function wrap(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }
}
