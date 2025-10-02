<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Contract;

interface IndexPolicyInterface
{
    /**
     * @param array-key $index
     * @return bool
     */
    public function indexIsValid($index): bool;
}
