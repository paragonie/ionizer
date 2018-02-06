<?php
/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 2/5/2018
 * Time: 3:07 PM
 */

namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\Contract\FilterInterface;
use ParagonIE\Ionizer\InputFilter;

/**
 * Class BoolFilter
 * @package ParagonIE\Ionizer\Filter
 */
class BoolFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected $default = false;

    /**
     * @var string
     */
    protected $type = 'bool';

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return bool
     * @throws \TypeError
     */
    public function process($data = null)
    {
        if (\is_array($data)) {
            throw new \TypeError(
                \sprintf('Unexpected array for boolean filter (%s).', $this->index)
            );
        }
        return (bool) parent::process(!empty($data));
    }

    /**
     * Sets the expected input type (e.g. string, boolean)
     *
     * @param string $typeIndicator
     * @return FilterInterface
     * @throws \TypeError
     */
    public function setType(string $typeIndicator): FilterInterface
    {
        if ($typeIndicator !== 'bool') {
            throw new \TypeError(
                'Type must always be set to "bool".'
            );
        }
        return parent::setType('bool');
    }

    /**
     * Set the default value (not applicable to booleans)
     *
     * @param mixed $value
     * @return FilterInterface
     */
    public function setDefault($value): FilterInterface
    {
        return parent::setDefault($value);
    }
}
