<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;

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
}
