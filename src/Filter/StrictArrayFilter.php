<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\Ionizer\Contract\FilterInterface;
use ParagonIE\Ionizer\Util;

/**
 * Class StrictArrayFilter
 * @package ParagonIE\Ionizer\Filter
 */
class StrictArrayFilter extends ArrayFilter implements FilterInterface
{
    /** @var string */
    protected $keyType;

    /** @var string */
    protected $valueType;

    public function __construct(string $keyType, string $valueType)
    {
        if (!\in_array($keyType, ['int', 'string'], true)) {
            throw new \RuntimeException('Cannot accept key types other than "int" or "string".');
        }
        $this->keyType = $keyType;
        $this->valueType = $valueType;
    }

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed|null $data
     * @param int $offset
     * @return mixed
     * @throws \TypeError
     */
    public function applyCallbacks($data = null, int $offset = 0)
    {
        if ($offset === 0) {
            if (\is_null($data)) {
                return parent::applyCallbacks([], 0);
            } elseif (!\is_array($data)) {
                throw new \TypeError(
                    \sprintf('Expected an array (%s).', $this->index)
                );
            }
            $data = (array) $data;
            /**
             * @var array<mixed, mixed> $data
             * @var string|int|float|bool|array|null $value
             */
            foreach ($data as $key => $value) {
                $keyType = Util::getType($key);
                $valType = Util::getType($value);
                if ($keyType !== $this->keyType || $valType !== $this->valueType) {
                    throw new \TypeError(
                        \sprintf(
                            'Expected an array<%s, %s>. At least one element of <%s, %s> was found (%s[%s] == %s).',
                            $this->keyType,
                            $this->valueType,
                            $keyType,
                            $valType,
                            $this->index,
                            \json_encode($key),
                            \json_encode($value)
                        )
                    );
                }
            }

            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
