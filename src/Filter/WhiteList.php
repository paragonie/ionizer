<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InputFilter;

/**
 * Class WhiteList
 * @package ParagonIE\Ionizer\Filter
 */
class WhiteList extends InputFilter
{
    /**
     * @var array<int, mixed>
     */
    protected $allowedValues = [];

    /**
     * WhiteList constructor.
     * @param mixed ...$values
     */
    public function __construct(...$values)
    {
        $this->addToWhiteList(...$values);
    }

    /**
     * @param array<int, string|int|float|bool|null> values
     * @return self
     */
    protected function addToWhiteList(...$values)
    {
        switch ($this->type) {
            case 'bool':
                /**
                 * @var array<int, bool> $values
                 * @var bool $val
                 */
                foreach ($values as $val) {
                    $this->allowedValues []= (bool) $val;
                }
                break;
            case 'float':
                /**
                 * @var array<int, float> $values
                 * @var float $val
                 */
                foreach ($values as $val) {
                    $this->allowedValues []= (float) $val;
                }
                break;
            case 'int':
                /**
                 * @var array<int, int> $values
                 * @var int $val
                 */
                foreach ($values as $val) {
                    $this->allowedValues []= (int) $val;
                }
                break;
            case 'string':
                /**
                 * @var array<int, string> $values
                 * @var string $val
                 */
                foreach ($values as $val) {
                    $this->allowedValues []= (string) $val;
                }
                break;
            default:
                /**
                 * @var array<int, string> $values
                 * @var string $val
                 */
                foreach ($values as $val) {
                    $this->allowedValues []= $val;
                }
        }
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return mixed
     * @throws \TypeError
     */
    public function process($data = null)
    {
        if (!empty($this->allowedValues)) {
            if (!\in_array($data, $this->allowedValues, true)) {
                $data = null;
            }
        }

        /** @var string|int|float|bool|null $data */
        $data = $this->applyCallbacks($data, 0);
        if ($data === null) {
            /** @var string|int|float|bool|null $data */
            $data = $this->default;
        }

        // For type strictness:
        switch ($this->type) {
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
}
