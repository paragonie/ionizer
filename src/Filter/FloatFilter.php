<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;

/**
 * Class FloatFilter
 * @package ParagonIE\Ionizer\Filter
 */
class FloatFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected $default = 0;

    /**
     * @var float|null
     */
    protected $max = null;

    /**
     * @var float|null
     */
    protected $min = null;

    /**
     * @var string
     */
    protected $type = 'float';

    /**
     * @param float|null $value
     * @return self
     * @throws \TypeError
     */
    public function setMaximumValue($value = null): self
    {
        if (\is_null($value)) {
            $this->max = $value;
            return $this;
        }
        if (!\is_numeric($value)) {
            throw new \TypeError('A number was expected. ' . \gettype($value) . ' given.');
        }
        $this->max = (float) $value;
        return $this;
    }

    /**
     * @param float|null $value
     * @return self
     * @throws \TypeError
     */
    public function setMinimumValue($value = null): self
    {
        if (\is_null($value)) {
            $this->min = $value;
            return $this;
        }
        if (!\is_numeric($value)) {
            throw new \TypeError('A number was expected. ' . \gettype($value) . ' given.');
        }
        $this->min = (float) $value;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return float
     * @throws \TypeError
     */
    public function process($data = null)
    {
        if (\is_array($data)) {
            throw new \TypeError(
                \sprintf('Unexpected array for float filter (%s).', $this->index)
            );
        }
        if (\is_int($data) || \is_float($data)) {
            $data = (float) $data;
        } elseif (\is_null($data) || $data === '') {
            $data = null;
        } elseif (\is_string($data) && \is_numeric($data)) {
            $data = (float) $data;
        } else {
            throw new \TypeError(
                \sprintf('Expected an integer or floating point number (%s).', $this->index)
            );
        }

        if (!\is_null($this->min) && !\is_null($data)) {
            if ($data < $this->min) {
                $data = null;
            }
        }
        if (!\is_null($this->max) && !\is_null($data)) {
            if ($data > $this->max) {
                $data = null;
            }
        }

        return (float) parent::process($data);
    }
}
