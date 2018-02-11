<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;

/**
 * Class IntFilter
 * @package ParagonIE\Ionizer\Filter
 */
class IntFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected $default = 0;

    /**
     * @var int|null
     */
    protected $max = null;

    /**
     * @var int|null
     */
    protected $min = null;

    /**
     * @var string
     */
    protected $type = 'int';

    /**
     * @param mixed|null $value
     * @return self
     * @throws \TypeError
     */
    public function setMaximumValue($value = null): self
    {
        if (\is_null($value)) {
            $this->max = $value;
            return $this;
        }
        if (!\is_int($value)) {
            throw new \TypeError('An integer was expected. ' . \gettype($value) . ' given.');
        }
        $this->max = (int) $value;
        return $this;
    }

    /**
     * @param mixed|null $value
     * @return self
     * @throws \TypeError
     */
    public function setMinimumValue($value = null): self
    {
        if (\is_null($value)) {
            $this->min = $value;
            return $this;
        }
        if (!\is_int($value)) {
            throw new \TypeError('An integer was expected. ' . \gettype($value) . ' given.');
        }
        $this->min = (int) $value;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return int
     * @throws \TypeError
     */
    public function process($data = null)
    {
        if (\is_array($data)) {
            throw new \TypeError(
                \sprintf('Unexpected array for integer filter (%s).', $this->index)
            );
        }
        if (\is_int($data) || \is_float($data)) {
            $data = (int) $data;
        } elseif (\is_null($data) || $data === '') {
            $data = null;
        } elseif (\is_string($data) && \preg_match('#^\-?[0-9]+$#', $data)) {
            $data = (int) $data;
        } else {
            throw new \TypeError(
                \sprintf('Expected an integer (%s).', $this->index)
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

        return (int) parent::process($data);
    }
}
