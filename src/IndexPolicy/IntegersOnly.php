<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\IndexPolicy;

use ParagonIE\Ionizer\Contract\IndexPolicyInterface;

class IntegersOnly implements IndexPolicyInterface
{
    /**
     * Any integer key is valid.
     *
     * @param array-key $index
     * @return bool
     */
    public function indexIsValid($index): bool
    {
        return is_int($index);
    }
}
