<?php

namespace App\Helper;

use Symfony\Component\Yaml\Yaml;

/**
 * Class ArrayHelper
 * @package Revizto\TranslatorBundle
 *
 * @author  Sergey Koksharov <sharoff45@gmail.com>
 */
class ArrayHelper
{
    /**
     * @param array $dottedArray
     *
     * @return array
     */
    public static function extractFromDotArray(array $dottedArray): array
    {
        $result = [];
        foreach ($dottedArray as $dottedKey => $item) {
            self::set($result, $dottedKey, $item);
        }

        return $result;
    }

    public static function dot(array $array, $prepend = '', array &$results = [])
    {
        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                self::dot($value,  $prepend . $key . '.', $results);
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * @param array $original
     * @param array $fillingArray
     */
    public static function fillRecursive(array &$original, array $fillingArray): void
    {
        foreach ($fillingArray as $fillKey => $fillValue) {
            if (\is_array($fillValue)) {
                if (!array_key_exists($fillKey, $original)) {
                    $original[$fillKey] = [];
                }

                self::fillRecursive($original[$fillKey], $fillValue);
                continue;
            }

            $original[$fillKey] = $fillValue;
        }
    }

    /**
     * @param array $array
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    private static function set(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }
}
