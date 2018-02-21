<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;
use ParagonIE\Ionizer\InvalidDataException;

/**
 * Class ArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class ArrayFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected $default = [];

    /**
     * @var string
     */
    protected $type = 'array';

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return array
     * @throws \TypeError
     * @throws InvalidDataException
     */
    public function process($data = null)
    {
        if (\is_array($data)) {
            $data = (array) $data;
        } elseif (\is_null($data)) {
            $data = [];
        } else {
            throw new \TypeError(
                \sprintf('Expected an array (%s).', $this->index)
            );
        }
        return (array) parent::process($data);
    }
}
