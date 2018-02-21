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
     * @param string $str
     * @param string $token
     * @return array<int, string>
     */
    public static function chunk(string $str, string $token = '/'): array
    {
        return \explode(
            $token,
            \trim($str, $token)
        );
    }

    /**
     * @param mixed $input
     * @return string
     * @throws \TypeError
     */
    public static function getType($input): string
    {
        if (\is_null($input)) {
            return 'null';
        }
        if (\is_callable($input)) {
            return 'callable';
        }
        if (\is_resource($input)) {
            return 'resource';
        }
        if (\is_object($input)) {
            return \get_class($input);
        }
        if (\is_string($input)) {
            return 'string';
        }
        $type = \gettype($input);
        switch ($type) {
            case 'boolean':
                return 'bool';
            case 'double':
                return 'float';
            case 'integer':
                return 'int';
        }
        throw new \TypeError('Unknown type');
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
