<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

class DateFormatterFactory
{
    private DateFormatOptions $defaultOptions;

    public function __construct(?DateFormatOptions $defaultOptions = null)
    {
        if ($defaultOptions !== null) {
            $this->defaultOptions = $defaultOptions;
        } else {
            $this->defaultOptions = new DateFormatOptions('yyyy-MM-dd');
            $this->defaultOptions->setLocale('nl_NL');
            $this->defaultOptions->setTimezone('Europe/Amsterdam');
        }
    }

    public function create(DateFormatOptions $options): IntlDateFormatter
    {
        $locale = $options->getLocale() ?? $this->defaultOptions->getLocale();
        $timezone = $options->getTimezone() ?? $this->defaultOptions->getTimezone();

        return new IntlDateFormatter(
            $locale,
            $options->getDateType(),
            $options->getTimeType(),
            $timezone,
            $options->getCalendar(),
            $options->getPattern()
        );
    }
}
