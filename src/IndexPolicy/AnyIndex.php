<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\IndexPolicy;

use ParagonIE\Ionizer\Contract\IndexPolicyInterface;

class AnyIndex implements IndexPolicyInterface
{
    /**
     * Any integer or string index is valid.
     *
     * @param array-key $index
     * @return bool
     */
    public function indexIsValid($index): bool
    {
        return is_string($index) || is_int($index);
    }
}
