<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\Util;

/**
 * Class StringArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class StringArrayFilter extends ArrayFilter
{
    /**
     * @var string
     */
    protected $type = 'string[]';

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
                return parent::applyCallbacks([], 0);
            } elseif (!\is_array($data)) {
                throw new \TypeError(
                    \sprintf('Expected an array of string (%s).', $this->index)
                );
            }
            /** @var array<string, string> $data */
            $data = (array) $data;
            if (!Util::is1DArray((array) $data)) {
                throw new \TypeError(
                    \sprintf('Expected a 1-dimensional array (%s).', $this->index)
                );
            }
            /** @var string|null $val */
            foreach ($data as $key => $val) {
                if (\is_array($val)) {
                    throw new \TypeError(
                        \sprintf('Expected a 1-dimensional array (%s).', $this->index)
                    );
                }
                if (\is_null($val)) {
                    $data[$key] = '';
                } elseif (\is_numeric($val)) {
                    $data[$key] = (string) $val;
                } elseif (!\is_string($val)) {
                    throw new \TypeError(
                        \sprintf('Expected a string at index %s (%s).', $key, $this->index)
                    );
                }
            }
            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
