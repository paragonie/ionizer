<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Contract;

/**
 * Interface FilterContainerInterface
 * @package ParagonIE\Ionizer\Contract
 */
interface FilterContainerInterface
{
    /**
     * @param string $path
     * @param FilterInterface $filter
     * @return self
     */
    public function addFilter(string $path, FilterInterface $filter): self;

    /**
     * @param string $path
     * @return array<array-key, FilterInterface>
     */
    public function getFiltersForPath(string $path);
}
