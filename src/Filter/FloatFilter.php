<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;
use ParagonIE\Ionizer\InvalidDataException;
use ReturnTypeWillChange;
use TypeError;
use function gettype;
use function is_array;
use function is_float;
use function is_int;
use function is_null;
use function is_numeric;
use function is_string;
use function sprintf;

/**
 * Class FloatFilter
 * @package ParagonIE\Ionizer\Filter
 */
class FloatFilter extends InputFilter
{
    protected mixed $default = 0;

    protected ?float $max = null;

    protected ?float $min = null;

    protected string $type = 'float';

    /**
     * @throws TypeError
     */
    public function setMaximumValue(?float $value = null): static
    {
        if (is_null($value)) {
            $this->max = $value;
            return $this;
        }
        if (!is_numeric($value)) {
            throw new TypeError('A number was expected. ' . gettype($value) . ' given.');
        }
        $this->max = (float) $value;
        return $this;
    }

    /**
     * @throws TypeError
     */
    public function setMinimumValue(?float $value = null): static
    {
        if (is_null($value)) {
            $this->min = $value;
            return $this;
        }
        if (!is_numeric($value)) {
            throw new TypeError('A number was expected. ' . gettype($value) . ' given.');
        }
        $this->min = (float) $value;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return float
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function process(mixed $data = null): float
    {
        if (is_array($data)) {
            throw new TypeError(
                sprintf('Unexpected array for float filter (%s).', $this->index)
            );
        }
        if (is_int($data) || is_float($data)) {
            $data = (float) $data;
        } elseif (is_null($data) || $data === '') {
            $data = null;
        } elseif (is_string($data) && is_numeric($data)) {
            $data = (float) $data;
        } else {
            throw new TypeError(
                sprintf('Expected an integer or floating point number (%s).', $this->index)
            );
        }

        if (!is_null($this->min) && !is_null($data)) {
            if ($data < $this->min) {
                $data = null;
            }
        }
        if (!is_null($this->max) && !is_null($data)) {
            if ($data > $this->max) {
                $data = null;
            }
        }

        return (float) parent::process($data);
    }
}
