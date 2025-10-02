<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\Contract\IndexPolicyInterface;
use ParagonIE\Ionizer\InputFilter;
use ParagonIE\Ionizer\InvalidDataException;
use ReturnTypeWillChange;
use TypeError;
use function is_array;
use function is_null;
use function sprintf;

/**
 * Class ArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class ArrayFilter extends InputFilter
{
    protected mixed $default = [];

    protected string $type = 'array';

    protected ?IndexPolicyInterface $indexPolicy = null;

    /**
     * Add restrictions to the keys allowed in this array
     *
     * @param IndexPolicyInterface $indexPolicy
     * @return static
     */
    public function setIndexPolicy(IndexPolicyInterface $indexPolicy): static
    {
        $this->indexPolicy = $indexPolicy;
        return $this;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return array
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function process(mixed $data = null): array
    {
        if (is_array($data)) {
            $data = (array) $data;
        } elseif (is_null($data)) {
            $data = [];
        } else {
            throw new TypeError(
                sprintf('Expected an array (%s).', $this->index)
            );
        }
        if (!is_null($this->indexPolicy)) {
            $keys = array_keys($data);
            foreach ($keys as $arrayKey) {
                if (!$this->indexPolicy->indexIsValid($arrayKey)) {
                    throw new TypeError(
                        sprintf("Invalid key (%s) in violation of key policy", $arrayKey)
                    );
                }
            }
        }
        return (array) parent::process($data);
    }
}
