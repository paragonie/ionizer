<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter\Special;

use ParagonIE\ConstantTime\Binary;
use ParagonIE\Ionizer\Filter\StringFilter;
use ParagonIE\Ionizer\InvalidDataException;

/**
 * Class CreditCardNumberFilter
 * @package ParagonIE\Ionizer\Filter\Special
 */
class CreditCardNumberFilter extends StringFilter
{
    public function __construct()
    {
        $this->addCallback([__CLASS__, 'validateCreditCardNumber']);
    }

    /**
     * Validate a credit card number, based on input length and Luhn's Algorithm
     *
     * @param string $input
     *
     * @return string
     * @throws InvalidDataException
     */
    public static function validateCreditCardNumber(string $input): string
    {
        // Strip all non-decimal characters
        $stripped = \preg_replace('/[^0-9]/', '', $input);
        /** @var int $length */
        $length = Binary::safeStrlen($stripped);
        if ($length < 13 || $length > 19) {
            throw new InvalidDataException('Invalid credit card number (invalid length)');
        }
        /** @var array<int, string> $split */
        $split = \str_split($stripped, 1);

        /** @var int $calc */
        $calc = 0;
        /** @var int $l */
        $l = \count($split);
        for ($i = 0; $i < $l; ++$i) {
            /** @var int $n */
            $n = $split[$l - $i - 1] << ($i & 1);
            if ($n > 9) {
                $n = ((int) ($n / 10)) + ($n % 10);
            }
            $calc += $n;
        }

        if ($calc % 10 !== 0) {
            throw new InvalidDataException('Invalid credit card number (Luhn)');
        }
        return $stripped;
    }
}
