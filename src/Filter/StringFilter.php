<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter;

use ParagonIE\ConstantTime\Binary;
use ParagonIE\Ionizer\InputFilter;
use ParagonIE\Ionizer\InvalidDataException;
use ReturnTypeWillChange;
use TypeError;
use function is_array;
use function is_null;
use function is_numeric;
use function is_object;
use function preg_replace;
use function sprintf;

/**
 * Class StringFilter
 * @package ParagonIE\Ionizer\Filter
 */
class StringFilter extends InputFilter
{
    /**
     * @var mixed
     */
    protected mixed $default = '';

    /**
     * @var string
     */
    protected string $pattern = '';

    /**
     * @var string
     */
    protected string $type = 'string';

    /**
     * @param string $input
     * @return string
     * @throws InvalidDataException
     */
    public static function nonEmpty(string $input): string
    {
        if (Binary::safeStrlen($input) < 1) {
            throw new InvalidDataException('String cannot be empty');
        }
        return $input;
    }

    /**
     * Process data using the filter rules.
     *
     * @param mixed $data
     * @return string
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function process(mixed $data = null): string
    {
        if (is_array($data)) {
            throw new TypeError(
                sprintf('Unexpected array for string filter (%s).', $this->index)
            );
        }
        if (\is_string($data)) {
            // continue
        } elseif (is_object($data) && \method_exists($data, '__toString')) {
            $data = (string)$data->__toString();
        } elseif (is_numeric($data)) {
            $data = (string)$data;
        } elseif (is_null($data)) {
            $data = null;
        } else {
            throw new TypeError(
                sprintf('Expected a string (%s).', $this->index)
            );
        }
        return (string) parent::process($data);
    }

    /**
     * Set a regular expression pattern that the input string
     * must match.
     */
    public function setPattern(string $pattern = ''): static
    {
        if (empty($pattern)) {
            $this->pattern = '';
        } else {
            $this->pattern = '#' . preg_replace('/([^\\\\])#/', '$1\\#', $pattern) . '#';
        }
        return $this;
    }

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed $data
     * @param int $offset
     * @return mixed
     * @throws InvalidDataException
     * @throws TypeError
     */
    #[ReturnTypeWillChange]
    public function applyCallbacks(mixed $data = null, int $offset = 0): string
    {
        if ($offset === 0) {
            if (!empty($this->pattern)) {
                if (!\preg_match((string) $this->pattern, (string) $data)) {
                    throw new InvalidDataException(
                        sprintf('Pattern match failed (%s).', $this->index)
                    );
                }
            }
            return parent::applyCallbacks($data, 0);
        }
        return parent::applyCallbacks($data, $offset);
    }
}
