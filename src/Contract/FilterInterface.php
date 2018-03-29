<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Contract;

use ParagonIE\Ionizer\InvalidDataException;

/**
 * Interface FilterInterface
 * @package ParagonIE\Ionizer\Contract
 */
interface FilterInterface
{
    /**
     * Sets the expected input type (e.g. string, boolean)
     *
     * @param string $typeIndicator
     * @return FilterInterface
     */
    public function setType(string $typeIndicator): FilterInterface;

    /**
     * Set the default value (not applicable to booleans)
     *
     * @param string|int|float|bool|array|null $value
     * @return FilterInterface
     */
    public function setDefault($value): FilterInterface;

    /**
     * Add a callback to this filter (supports more than one)
     *
     * @param callable $func
     * @return FilterInterface
     */
    public function addCallback(callable $func): FilterInterface;

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return mixed
     * @throws InvalidDataException
     */
    public function process($data);


    /**
     * @param string $index
     * @return FilterInterface
     */
    public function setIndex(string $index): FilterInterface;
}
