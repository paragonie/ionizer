<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer;

use ParagonIE\Ionizer\Contract\{
    FilterInterface,
    FilterContainerInterface
};

/**
 * Class InputFilterContainer
 *
 * Contains a set of filter rules, useful for enforcing a strict type on
 * unstructured data (e.g. HTTP POST parameters).
 *
 * @package ParagonIE\Ionizer
 */
abstract class InputFilterContainer implements FilterContainerInterface
{
    /** @const string SEPARATOR */
    const SEPARATOR = '.';

    /**
     * @var array<string, array<mixed, FilterInterface>>
     */
    protected $filterMap = [];

    /**
     * InputFilterContainer constructor.
     */
    abstract public function __construct();

    /**
     * Add a new filter to this input value
     *
     * @param string $path
     * @param FilterInterface $filter
     * @return FilterContainerInterface
     */
    public function addFilter(
        string $path,
        FilterInterface $filter
    ): FilterContainerInterface {
        if (!isset($this->filterMap[$path])) {
            $this->filterMap[$path] = [];
        }
        /** @psalm-suppress MixedArrayAssignment */
        $this->filterMap[$path][] = $filter->setIndex($path);
        return $this;
    }

    /**
     * @param string $path
     * @return array<array-key, FilterInterface>
     */
    public function getFiltersForPath(string $path)
    {
        if (!isset($this->filterMap[$path])) {
            $this->filterMap[$path] = [];
        }
        return $this->filterMap[$path];
    }

    /**
     * Use firstlevel.second_level.thirdLevel to find indices in an array
     *
     * @param string $key
     * @param mixed $multiDimensional
     * @return mixed
     * @throws \Error
     * @throws InvalidDataException
     *
     * @psalm-suppress UnusedVariable
     */
    public function filterValue(string $key, $multiDimensional)
    {
        /** @var array<int, string> $pieces */
        $pieces = Util::chunk($key, (string) static::SEPARATOR);
        /** @var array|string $filtered */
        $filtered =& $multiDimensional;

        $var = '$multiDimensional';
        if (\is_array($multiDimensional)) {
            foreach ($pieces as $piece) {
                $_var = substr($var, 1);
                if (empty(${$_var})) {
                    ${$_var} = [];
                }
                ksort(${$_var});

                $append = '[' . self::sanitize($piece) . ']';
                if (!isset(${$var . $append})) {
                    ${$var . $append} = null;
                }
                $var .= $append;
            }
            /**
             * @security This shouldn't be escapable. We know eval is evil, but
             *           there's not a more elegant way to process this in PHP.
             */
            eval('$filtered =& ' . $var. ';');
        }

        // If we have filters, let's apply them:
        if (isset($this->filterMap[$key])) {
            /** @var object|null $filter */
            foreach ($this->filterMap[$key] as $filter) {
                if ($filter instanceof FilterInterface) {
                    /** @var string|int|bool|float|array $filtered */
                    $filtered = $filter->process($filtered);
                }
            }
        }
        return $multiDimensional;
    }

    /**
     * Use firstlevel.second_level.thirdLevel to find indices in an array
     *
     * Doesn't apply filters
     *
     * @param string $key
     * @param array<string, string|array> $multiDimensional
     * @return mixed
     * @psalm-suppress PossiblyInvalidArrayOffset
     */
    public function getUnfilteredValue(string $key, array $multiDimensional = [])
    {
        /** @var array<string, string> $pieces */
        $pieces = Util::chunk($key, (string) static::SEPARATOR);

        /** @var string|array<string, string|array> $value */
        $value = $multiDimensional;

        /**
         * @var array<string, string> $pieces
         * @var string $piece
         */
        foreach ($pieces as $piece) {
            if (!isset($value[$piece])) {
                return null;
            }
            /** @var string|array<string, string|array> $next */
            $next = $value[$piece];

            /** @var string|array<string, string|array> $value */
            $value = $next;
        }
        return $value;
    }

    /**
     * Only allow allow printable ASCII characters:
     *
     * @param string $input
     * @return string
     * @throws \Error
     */
    protected static function sanitize(string $input): string
    {
        /** @var string|bool $sanitized */
        $sanitized = \json_encode(
            \preg_replace('#[^\x20-\x7e]#', '', $input)
        );
        if (!\is_string($sanitized)) {
            throw new \Error('Could not sanitize string');
        }
        return $sanitized;
    }

    /**
     * Process the input array.
     *
     * @param array $dataInput
     * @return array
     * @throws \Error
     * @throws InvalidDataException
     */
    public function __invoke(array $dataInput = []): array
    {
        /** @var string $key */
        foreach (\array_keys($this->filterMap) as $key) {
            /** @var array $dataInput */
            $dataInput = $this->filterValue($key, $dataInput);
        }
        return $dataInput;
    }
}
