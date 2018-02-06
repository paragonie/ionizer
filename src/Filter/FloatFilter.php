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
     * @var string
     */
    protected $type = 'float';

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
        return (float) parent::process($data);
    }
}
