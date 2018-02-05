<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer;

/**
 * Class Util
 * @package ParagonIE\Ionizer
 */
abstract class Util
{
    /**
     * A wrapper for explode($a, trim($b, $a))
     *
     * @param $str
     * @param $token
     * @return array
     */
    public static function chunk(string $str, string $token = '/'): array
    {
        return \explode(
            $token,
            \trim($str, $token)
        );
    }

    /**
     * Returns true if every member of an array is NOT another array
     *
     * @param array $source
     * @return bool
     */
    public static function is1DArray(array $source): bool
    {
        return \count($source) === \count($source, \COUNT_RECURSIVE);
    }
}
