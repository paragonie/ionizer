<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\InvalidDataException;
use ParagonIE\Ionizer\Util;
use ReturnTypeWillChange;
use function is_array;
use function is_null;
use function is_string;
use function sprintf;

/**
 * Class FloatArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class FloatArrayFilter extends ArrayFilter
{
    /**
     * @var float
     */
    protected mixed $default = 0.0;

    /**
     * @var string
     */
    protected string $type = 'float[]';

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed $data
     * @param int $offset
     * @return mixed
     * @throws \TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function applyCallbacks(mixed $data = null, int $offset = 0): array
    {
        if ($offset === 0) {
            if (is_null($data)) {
                return parent::applyCallbacks($data, 0);
            } elseif (!is_array($data)) {
                throw new \TypeError(
                    sprintf('Expected an array of floats (%s).', $this->index)
                );
            }

            /** @var array<string, float> $data */
            $data = (array) $data;
            if (!Util::is1DArray($data)) {
                throw new \TypeError(
                    sprintf('Expected a 1-dimensional array (%s).', $this->index)
                );
            }

            /**
             * @var string|int|float|bool|array|null $val
             */
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    throw new \TypeError(
                        sprintf('Expected a 1-dimensional array (%s).', $this->index)
                    );
                }
                if (\is_int($val) || \is_float($val)) {
                    $data[$key] = (float) $val;
                } elseif (is_null($val) || $val === '') {
                    $data[$key] = (float) $this->default;
                } elseif (is_string($val) && \is_numeric($val)) {
                    $data[$key] = (float) $val;
                } else {
                    throw new \TypeError(
                        sprintf('Expected a float at index %s (%s).', $key, $this->index)
                    );
                }
            }
            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
