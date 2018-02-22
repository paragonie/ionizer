<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter\Special;

use ParagonIE\Ionizer\Filter\StringFilter;
use ParagonIE\Ionizer\InvalidDataException;

/**
 * Class DateTimeFilter
 * @package ParagonIE\Ionizer\Filter\Special
 */
class DateTimeFilter extends StringFilter
{
    /** @var string $dateTimeFormat */
    protected $dateTimeFormat;

    /** @var \DateTimeZone|null $tz */
    protected $tz;

    /**
     * DateTimeFilter constructor.
     *
     * @param string $format
     * @param \DateTimeZone|null $tz
     */
    public function __construct(
        string $format = \DateTime::ATOM,
        \DateTimeZone $tz = null
    ) {
        $this->dateTimeFormat = $format;
        $this->tz = $tz;
    }

    /**
     * Apply all of the callbacks for this filter.
     *
     * @param mixed|null $data
     * @param int $offset
     * @return mixed
     * @throws \TypeError
     * @throws InvalidDataException
     */
    public function applyCallbacks($data = null, int $offset = 0)
    {
        if ($offset === 0) {
            if (!\is_null($data)) {
                try {
                    /** @var string $dt */
                    $data = (new \DateTime($data, $this->tz))
                        ->format($this->dateTimeFormat);
                } catch (\Exception $ex) {
                    throw new InvalidDataException(
                        'Invalid date/time',
                        0,
                        $ex
                    );
                }
            }
        }
        return parent::applyCallbacks($data, $offset);
    }
}
