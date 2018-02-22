<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter\Special;

use ParagonIE\Ionizer\Filter\StringFilter;
use ParagonIE\Ionizer\InvalidDataException;

/**
 * Class EmailAddressFilter
 * @package ParagonIE\Ionizer\Filter\Special
 */
class EmailAddressFilter extends StringFilter
{
    public function __construct()
    {
        $this->addCallback([__CLASS__, 'validateEmailAddress']);
    }

    /**
     * @param string $input
     *
     * @return string
     * @throws InvalidDataException
     */
    public static function validateEmailAddress(string $input): string
    {
        /** @var string|bool $filtered */
        $filtered = \filter_var($input, FILTER_VALIDATE_EMAIL);
        if (!\is_string($filtered)) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        $pos = \strpos($filtered, '@');
        if ($pos === false) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        if ($pos === 0) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        if (\substr_count($filtered, '@') !== 1) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        /**
         * @var string $username
         * @var string $domain
         */
        list ($username, $domain) = \explode('@', $filtered);
        if (\preg_match('#^\.#', $username) || \preg_match('#\.$#', $username)) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        if (\strpos($filtered, '..') !== false) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        if (!\filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            throw new InvalidDataException('Invalid email address: ' . $input);
        }
        if (!\preg_match('#^\[?' . '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}' . '\]?$#', $domain)) {
            if (!\checkdnsrr($domain, 'MX')) {
                throw new InvalidDataException('Invalid email address: ' . $input);
            }
        }

        return $filtered;
    }
}