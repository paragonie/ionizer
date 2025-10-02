<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\IndexPolicy;

use ParagonIE\Ionizer\Contract\IndexPolicyInterface;

class IndexAllowList implements IndexPolicyInterface
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
     * @param array-key $index
     * @return bool
     */
    public function indexIsValid($index): bool
    {
        return in_array($index, $this->allowed, true);
    }
}
