<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Support;

/**
 * String helper utilities.
 *
 * Common string manipulation methods.
 *
 * @package WPZylos\Framework\Core
 */
class Str
{
    /**
     * Convert a string to snake_case.
     *
     * @param string $value String to convert
     *
     * @return string
     */
    public static function snake(string $value): string
    {
        $value = preg_replace('/\s+/u', '', ucwords($value));
        $value = preg_replace('/(.)(?=[A-Z])/u', '$1_', $value);

        return mb_strtolower($value);
    }

    /**
     * Convert a string to camelCase.
     *
     * @param string $value String to convert
     *
     * @return string
     */
    public static function camel(string $value): string
    {
        return lcfirst(self::studly($value));
    }

    /**
     * Convert a string to StudlyCase.
     *
     * @param string $value String to convert
     *
     * @return string
     */
    public static function studly(string $value): string
    {
        $words = explode(' ', str_replace([ '-', '_' ], ' ', $value));

        $studlyWords = array_map(static fn($word) => ucfirst($word), $words);

        return implode('', $studlyWords);
    }

    /**
     * Convert a string to a kebab-case.
     *
     * @param string $value String to convert
     *
     * @return string
     */
    public static function kebab(string $value): string
    {
        return str_replace('_', '-', self::snake($value));
    }

    /**
     * Determine if a string starts with a given substring.
     *
     * @param string $haystack String to search in
     * @param string|string[] $needles Substring(s) to find
     *
     * @return bool
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string ends with a given substring.
     *
     * @param string $haystack String to search in
     * @param string|string[] $needles Substring(s) to find
     *
     * @return bool
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a string contains a given substring.
     *
     * @param string $haystack String to search in
     * @param string|string[] $needles Substring(s) to find
     *
     * @return bool
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Limit a string to a given number of characters.
     *
     * @param string $value String to limit
     * @param int $limit Character limit
     * @param string $end Suffix to append if truncated
     *
     * @return string
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit) . $end;
    }

    /**
     * Generate a random string.
     *
     * @param int $length Length of string
     *
     * @return string
     * @throws \Random\RandomException
     */
    public static function random(int $length = 16): string
    {
        $string = '';

        while (( $len = strlen($string) ) < $length) {
            $size   = $length - $len;
            $bytes  = random_bytes($size);
            $string .= substr(str_replace([ '/', '+', '=' ], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Generate a URL-safe slug.
     *
     * @param string $value String to slugify
     * @param string $separator Word separator
     *
     * @return string
     */
    public static function slug(string $value, string $separator = '-'): string
    {
        // Transliterate
        $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII', $value);
        $value          = $transliterated !== false ? $transliterated : $value;

        // Convert to lowercase
        $value = mb_strtolower($value);

        // Remove special characters
        $value = preg_replace('/[^a-z0-9\s-]/', '', $value);

        // Replace spaces and multiple separators
        $value = preg_replace('/[\s-]+/', $separator, $value);

        // Trim separators
        return trim($value, $separator);
    }

    /**
     * Replace the first occurrence of a substring.
     *
     * @param string $search String to find
     * @param string $replace Replacement string
     * @param string $subject String to search in
     *
     * @return string
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        if ($search === '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Get the portion of a string before a given value.
     *
     * @param string $subject String to search in
     * @param string $search Value to find
     *
     * @return string
     */
    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, $search, true);

        return $result === false ? $subject : $result;
    }

    /**
     * Get the portion of a string after a given value.
     *
     * @param string $subject String to search in
     * @param string $search Value to find
     *
     * @return string
     */
    public static function after(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, $search);

        return $result === false ? $subject : substr($result, strlen($search));
    }
}
