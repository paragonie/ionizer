<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\IndexPolicy;

use ParagonIE\Ionizer\Contract\IndexPolicyInterface;

class StringsOnly implements IndexPolicyInterface
{
    /**
     * Any integer or string key is valid.
     *
     * @param array-key $index
     * @return bool
     */
    public function indexIsValid($index): bool
    {
        return is_string($index);
    }
}
