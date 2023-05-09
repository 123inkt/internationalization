<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTime;
use Exception;
use IntlDateFormatter;
use InvalidArgumentException;

/**
 * @phpstan-type DayOfTheWeek self::*DAY
 */
class DayOfTheWeekFormatter
{
    public const MONDAY    = 1;
    public const TUESDAY   = 2;
    public const WEDNESDAY = 3;
    public const THURSDAY  = 4;
    public const FRIDAY    = 5;
    public const SATURDAY  = 6;
    public const SUNDAY    = 7;

    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @phpstan-param DayOfTheWeek $isoNumericDay
     * @param int $isoNumericDay ISO-8601 numeric representation of the day of the week
     *
     * @return string Translated day of the week
     * @throws Exception
     */
    public function format(int $isoNumericDay, ?string $locale = null): string
    {
        if ($isoNumericDay < self::MONDAY || $isoNumericDay > self::SUNDAY) {
            throw new InvalidArgumentException(
                sprintf("'%d is not a valid ISO-8601 numeric representation of the day of the week.", $isoNumericDay)
            );
        }

        /** @var string|false $dayOfTheWeek */
        $dayOfTheWeek = IntlDateFormatter::formatObject(new DateTime('sunday +' . $isoNumericDay . ' days'), 'EEEE', $locale ?? $this->locale);
        if ($dayOfTheWeek === false) {
            // @codeCoverageIgnoreStart
            throw new DayOfTheWeekFormatException('Formatting day of the week failed');
        }
        // @codeCoverageIgnoreEnd

        return $dayOfTheWeek;
    }
}
