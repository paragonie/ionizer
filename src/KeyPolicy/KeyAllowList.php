<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\KeyPolicy;

use ParagonIE\Ionizer\Contract\KeyPolicyInterface;

class KeyAllowList implements KeyPolicyInterface
{
    /** @var array-key[] $allowed */
    private $allowed = [];

    /**
     * @param array-key ...$allowed
     */
    public function __construct(...$allowed)
    {
        $this->allowed = $allowed;
    }

    /**
     * Any integer or string key is valid.
     *
     * @param array-key $key
     * @return bool
     */
    public function keyIsValid($key): bool
    {
        return in_array($key, $this->allowed, true);
    }
}
