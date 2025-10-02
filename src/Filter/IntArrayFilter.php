<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InvalidDataException;
use ParagonIE\Ionizer\Util;
use ReturnTypeWillChange;
use TypeError;
use function is_array;
use function is_null;
use function is_string;
use function sprintf;

/**
 * Class IntArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class IntArrayFilter extends ArrayFilter
{
    /**
     * @var int
     */
    protected mixed $default = 0;

    /**
     * @var string
     */
    protected string $type = 'int[]';

    /**
     * Apply all of the callbacks for this filter.
     *
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function applyCallbacks(mixed $data = null, int $offset = 0): array
    {
        if ($offset === 0) {
            if (is_null($data)) {
                return parent::applyCallbacks($data, 0);
            } elseif (!is_array($data)) {
                throw new TypeError(
                    sprintf('Expected an array of integers (%s).', $this->index)
                );
            }
            /** @var array<string, int> $data */
            $data = (array) $data;
            if (!Util::is1DArray($data)) {
                throw new TypeError(
                    sprintf('Expected a 1-dimensional array (%s).', $this->index)
                );
            }
            /**
             * @var string|int|float|bool|array|null $val
             */
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    throw new TypeError(
                        sprintf('Expected a 1-dimensional array (%s).', $this->index)
                    );
                }
                if (\is_int($val) || \is_float($val)) {
                    $data[$key] = (int) $val;
                } elseif (is_null($val) || $val === '') {
                    $data[$key] = $this->default;
                } elseif (is_string($val) && \preg_match('#^-?[0-9]+$#', $val)) {
                    $data[$key] = (int) $val;
                } else {
                    throw new TypeError(
                        sprintf('Expected an integer at index %s (%s).', $key, $this->index)
                    );
                }
            }
            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
