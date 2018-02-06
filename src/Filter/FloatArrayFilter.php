<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\Util;

/**
 * Class FloatArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class FloatArrayFilter extends ArrayFilter
{
    /**
     * @var float
     */
    protected $default = 0.0;

    /**
     * @var string
     */
    protected $type = 'float[]';

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed $data
     * @param int $offset
     * @return mixed
     * @throws \TypeError
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress RedundantCondition
     */
    public function applyCallbacks($data = null, int $offset = 0)
    {
        if ($offset === 0) {
            if (\is_null($data)) {
                return parent::applyCallbacks($data, 0);
            } elseif (!\is_array($data)) {
                throw new \TypeError(
                    \sprintf('Expected an array of floats (%s).', $this->index)
                );
            }

            /** @var array<string, float> $data */
            $data = (array) $data;
            if (!Util::is1DArray($data)) {
                throw new \TypeError(
                    \sprintf('Expected a 1-dimensional array (%s).', $this->index)
                );
            }

            /** @var float|null $val */
            foreach ($data as $key => $val) {
                if (\is_array($val)) {
                    throw new \TypeError(
                        \sprintf('Expected a float at index %s (%s).', $key, $this->index)
                    );
                }
                if (\is_int($val) || \is_float($val)) {
                    $data[$key] = (float) $val;
                } elseif (\is_null($val) || $val === '') {
                    $data[$key] = (float) $this->default;
                } elseif (\is_string($val) && \is_numeric($val)) {
                    $data[$key] = (float) $val;
                } else {
                    throw new \TypeError(
                        \sprintf('Expected a float at index %s (%s).', $key, $this->index)
                    );
                }
            }
            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
