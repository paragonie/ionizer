<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;
use ParagonIE\Ionizer\InvalidDataException;
use ReturnTypeWillChange;
use TypeError;
use function gettype;
use function is_array;
use function is_int;
use function is_null;
use function is_string;
use function sprintf;

/**
 * Class IntFilter
 * @package ParagonIE\Ionizer\Filter
 */
class IntFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected mixed $default = 0;

    /**
     * @var int|null
     */
    protected ?int $max = null;

    /**
     * @var int|null
     */
    protected ?int $min = null;

    /**
     * @var string
     */
    protected string $type = 'int';

    /**
     * @throws TypeError
     */
    public function setMaximumValue(?int $value = null): static
    {
        $this->max = $value;
        return $this;
    }

    /**
     * @throws TypeError
     */
    public function setMinimumValue(?int $value = null): static
    {
        $this->min = $value;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return int
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function process(mixed $data = null): int
    {
        if (is_array($data)) {
            throw new TypeError(
                sprintf('Unexpected array for integer filter (%s).', $this->index)
            );
        }
        if (is_int($data) || \is_float($data)) {
            $data = (int) $data;
        } elseif (is_null($data) || $data === '') {
            $data = null;
        } elseif (is_string($data) && \preg_match('#^-?[0-9]+$#', $data)) {
            $data = (int) $data;
        } else {
            throw new TypeError(
                sprintf('Expected an integer (%s).', $this->index)
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

        return (int) parent::process($data);
    }
}
