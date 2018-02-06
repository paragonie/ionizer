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
     * @var string
     */
    protected $type = 'int';

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
        return (int) parent::process($data);
    }
}
