<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;
use IntlTimeZone;

/**
 * @phpstan-type CalendarType IntlDateFormatter::GREGORIAN|IntlDateFormatter::TRADITIONAL
 * @phpstan-type DateFormatType IntlTimeZone::*
 */
class DateFormatOptions
{
    /** @phpstan-var DateFormatType $dateType */
    protected int $dateType = IntlDateFormatter::FULL;

    /** @phpstan-var DateFormatType $timeType */
    protected int $timeType = IntlDateFormatter::FULL;

    /** @phpstan-var CalendarType $calendar */
    protected int $calendar = IntlDateFormatter::GREGORIAN;

    public function __construct(protected string $locale, protected string $timezone)
    {
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the preferred locale for the formatting. Expects an POSIX code (nl_NL, nl_BE, en_GB, etc...). Defaults to system configuration.
     * @return static
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * Set the preferred timezone for the formatting. Expects a timezone identifier (Europe/Amsterdam, UTC, etc...). Defaults to system configuration.
     * @return static
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @phpstan-return DateFormatType
     */
    public function getDateType(): int
    {
        return $this->dateType;
    }

    /**
     * @phpstan-param DateFormatType $dateType
     */
    public function setDateType(int $dateType): DateFormatOptions
    {
        $this->dateType = $dateType;

        return $this;
    }

    /**
     * @phpstan-return DateFormatType
     */
    public function getTimeType(): int
    {
        return $this->timeType;
    }

    /**
     * @phpstan-param DateFormatType $timeType
     */
    public function setTimeType(int $timeType): DateFormatOptions
    {
        $this->timeType = $timeType;

        return $this;
    }

    /**
     * @phpstan-return CalendarType
     */
    public function getCalendar(): int
    {
        return $this->calendar;
    }

    /**
     * @phpstan-param CalendarType $calendar
     */
    public function setCalendar(int $calendar): DateFormatOptions
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function __toString(): string
    {
        return "date:" . serialize([
                'locale' => $this->locale,
                'timezone' => $this->timezone,
                'dateType' => $this->dateType,
                'timeType' => $this->timeType,
                'calendar' => $this->calendar,
            ]);
    }
}
