<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\KeyPolicy;

use ParagonIE\Ionizer\Contract\KeyPolicyInterface;

class AnyKeys implements KeyPolicyInterface
{
    /**
     * Any integer or string key is valid.
     *
     * @param array-key $key
     * @return bool
     */
    public function keyIsValid($key): bool
    {
        return is_string($key) || is_int($key);
    }
}
