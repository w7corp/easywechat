<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Support;

use JetBrains\PhpStorm\Pure;

use function is_string;

class Arr
{
    /**
     * @param  mixed  $default
     */
    #[Pure]
    public static function get(mixed $array, string|int|null $key, mixed $default = null): mixed
    {
        if (! is_array($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', (string) $key) as $segment) {
            /** @phpstan-ignore-next-line */
            if (static::exists($array, $segment)) {
                /** @phpstan-ignore-next-line */
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * @param  array<int|string, mixed>  $array
     */
    public static function exists(array $array, string|int $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * @param  array<string|int, mixed>  $array
     * @return array<string|int, mixed>
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if (! is_string($key)) {
            $key = (string) $key;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param  array<string|int, mixed>  $array
     * @return array<string|int, mixed>
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * @param  array<string|int, mixed>  $array
     * @param  string|int|array<string|int, mixed>|null  $keys
     */
    #[Pure]
    public static function has(array $array, string|int|array|null $keys): bool
    {
        if (is_null($keys)) {
            return false;
        }

        $keys = (array) $keys;

        if (empty($array)) {
            return false;
        }

        if ([] === $keys) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            /** @phpstan-ignore-next-line */
            if (static::exists($array, $key)) {
                continue;
            }

            /** @phpstan-ignore-next-line */
            foreach (explode('.', (string) $key) as $segment) {
                /** @phpstan-ignore-next-line */
                if (static::exists($subKeyArray, $segment)) {
                    /** @phpstan-ignore-next-line */
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
