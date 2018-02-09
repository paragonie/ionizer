<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer;

use ParagonIE\ConstantTime\Binary;
use ParagonIE\Ionizer\Contract\FilterInterface;

/**
 * Class InputFilter
 * @package ParagonIE\Ionizer
 */
class InputFilter implements FilterInterface
{
    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string (for debugging purposes)
     */
    protected $index = '';

    /**
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * Sets the expected input type (e.g. string, boolean)
     *
     * @param string $typeIndicator
     * @return FilterInterface
     */
    public function setType(string $typeIndicator): FilterInterface
    {
        $this->type = $typeIndicator;
        return $this;
    }

    /**
     * Throw an InvalidDataException is this field is not defined/populated.
     * Use in a callback.
     *
     * $filter->addCallback([InputFilter::class, 'required']);
     *
     * @param mixed|null $data
     * @return mixed
     * @throws InvalidDataException
     */
    public static function required($data = null)
    {
        if (\is_null($data)) {
            throw new InvalidDataException('This is not an optional field.');
        }
        return $data;
    }

    /**
     * Set the default value (not applicable to booleans)
     *
     * @param mixed $value
     * @return FilterInterface
     */
    public function setDefault($value): FilterInterface
    {
        /** @psalm-suppress MixedAssignment */
        $this->default = $value;
        return $this;
    }

    /**
     * Add a callback to this filter (supports more than one)
     *
     * @param callable $func
     * @return FilterInterface
     */
    public function addCallback(callable $func): FilterInterface
    {
        $this->callbacks[] = $func;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return mixed
     * @throws \TypeError
     */
    public function process($data = null)
    {
        /** @psalm-suppress MixedAssignment */
        $data = $this->applyCallbacks($data, 0);
        if ($data === null) {
            /** @psalm-suppress MixedAssignment */
            $data = $this->default;
        }

        // For type strictness:
        switch ($this->type) {
            case 'array':
                return (array) $data;
            case 'bool':
                return (bool) $data;
            case 'float':
                return (float) $data;
            case 'int':
                return (int) $data;
            case 'string':
                return (string) $data;
            default:
                return $data;
        }
    }

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed $data
     * @param int $offset
     * @return mixed
     */
    public function applyCallbacks($data = null, int $offset = 0)
    {
        if (empty($data)) {
            if ($this->type === 'bool') {
                return false;
            }
            return $this->default;
        }
        if ($offset >= \count($this->callbacks)) {
            return $data;
        }
        $func = $this->callbacks[$offset];
        if (\is_callable($func)) {
            /** @psalm-suppress MixedAssignment */
            $data = $func($data);
        }
        return $this->applyCallbacks($data, $offset + 1);
    }

    /**
     * @param string $index
     * @return FilterInterface
     */
    public function setIndex(string $index): FilterInterface
    {
        $this->index = $index;
        return $this;
    }
}
