<?php
declare(strict_types=1);
namespace ParagonIE\Ionizer\Filter\Special;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use ParagonIE\Ionizer\Filter\StringFilter;
use ParagonIE\Ionizer\InvalidDataException;
use ReturnTypeWillChange;
use TypeError;

/**
 * Class DateTimeFilter
 * @package ParagonIE\Ionizer\Filter\Special
 */
class DateTimeFilter extends StringFilter
{
    protected string $dateTimeFormat;

    /** @var DateTimeZone|null $tz */
    protected ?DateTimeZone $tz;

    /**
     * DateTimeFilter constructor.
     *
     * @param string $format
     * @param DateTimeZone|null $tz
     */
    public function __construct(
        string $format = DateTimeInterface::ATOM,
        ?DateTimeZone $tz = null
    ) {
        $this->dateTimeFormat = $format;
        $this->tz = $tz;
    }

    /**
     * Apply all of the callbacks for this filter.
     *
     * @throws TypeError
     * @throws InvalidDataException
     */
    #[ReturnTypeWillChange]
    public function applyCallbacks(mixed $data = null, int $offset = 0): string
    {
        if ($offset === 0) {
            if (!\is_null($data)) {
                $data = (string) $data;
                try {
                    /** @var string $data */
                    $data = (new DateTime($data, $this->tz))
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
