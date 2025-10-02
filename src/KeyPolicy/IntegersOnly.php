<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\KeyPolicy;

use ParagonIE\Ionizer\Contract\KeyPolicyInterface;

class IntegersOnly implements KeyPolicyInterface
{
    /**
     * Any integer key is valid.
     *
     * @param array-key $key
     * @return bool
     */
    public function keyIsValid($key): bool
    {
        return is_int($key);
    }
}
