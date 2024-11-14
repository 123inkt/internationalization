<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

class DateFormatOptions
{
    protected int $dateType = IntlDateFormatter::FULL;
    protected int $timeType = IntlDateFormatter::FULL;
    protected int $calendar = IntlDateFormatter::GREGORIAN;

    public function __construct(protected string $locale, protected string $timezone)
    {
    }

    public function getLocale():?string
    {
        return $this->locale;
    }

    /**
     * Set the preferred locale for the formatting. Expects an ietf code (nl_NL, nl_BE, en_GB, etc...). Defaults to system configuration.
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

    public function setDateType(int $dateType): DateFormatOptions
    {
        $this->dateType = $dateType;

        return $this;
    }

    public function getDateType(): int
    {
        return $this->dateType;
    }

    public function setTimeType(int $timeType): DateFormatOptions
    {
        $this->timeType = $timeType;

        return $this;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    public function setCalendar(int $calendar): DateFormatOptions
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function getCalendar(): int
    {
        return $this->calendar;
    }
}
