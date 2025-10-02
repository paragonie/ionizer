<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Contract;

interface KeyPolicyInterface
{
    /**
     * @param array-key $key
     * @return bool
     */
    public function keyIsValid($key): bool;
}
