<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer;

use ParagonIE\ConstantTime\Binary;
use ParagonIE\Ionizer\Contract\FilterInterface;
use TypeError;

/**
 * Class InputFilter
 * @package ParagonIE\Ionizer
 */
class InputFilter implements FilterInterface
{
    /**
     * @var string|int|float|bool|array|null
     */
    protected mixed $default;

    protected string $type = '';

    /**
     * @var string (for debugging purposes)
     */
    protected string $index = '';

    /**
     * @var callable[]
     */
    protected array $callbacks = [];

    /**
     * @var string[]
     */
    protected array $thisCallbacks = [];

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
     * @param string|int|float|bool|array|null $value
     * @return FilterInterface
     */
    public function setDefault($value): FilterInterface
    {
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
     * Add a callback to this filter (supports more than one)
     *
     * @param string $func
     * @return FilterInterface
     */
    public function addThisCallback(string $func): FilterInterface
    {
        if (!\method_exists($this, $func)) {
            throw new \Error('Method ' . $func . ' does not exist on class ' . \get_class($this));
        }
        $this->thisCallbacks[] = $func;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return mixed
     * @throws InvalidDataException
     * @throws TypeError
     */
    public function process(mixed $data = null): mixed
    {
        /** @var string|int|float|bool|array|null $data */
        $data = $this->applyCallbacks($data, 0);
        /** @var string|int|float|bool|array|null $data */
        $data = $this->applyThisCallbacks($data, 0);
        if ($data === null) {
            /** @var string|int|float|bool|array|null $data */
            $data = $this->default;
        }

        // For type strictness:
        switch ($this->type) {
            case 'array':
                /** @var array $data */
                return (array) $data;
            case 'bool':
                /** @var bool $data */
                return (bool) $data;
            case 'float':
                /** @var float $data */
                return (float) $data;
            case 'int':
                /** @var int $data */
                return (int) $data;
            case 'string':
                /** @var string $data */
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
     * @throws InvalidDataException
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
        /** @var string|int|float|bool|array|null $data */
        $data = $func($data);
        return $this->applyCallbacks($data, $offset + 1);
    }
    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed $data
     * @param int $offset
     * @return mixed
     * @throws InvalidDataException
     */
    public function applyThisCallbacks($data = null, int $offset = 0)
    {
        if ($offset >= \count($this->thisCallbacks)) {
            return $data;
        }
        /** @var string $func */
        $func = $this->thisCallbacks[$offset];
        /** @var string|int|float|bool|array|null $data */
        $data = $this->$func($data);
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
