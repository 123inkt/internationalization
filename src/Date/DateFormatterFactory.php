<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

/**
 * @internal
 */
class DateFormatterFactory
{
    public function create(DateFormatOptions $options, string $pattern): IntlDateFormatter
    {
        return new IntlDateFormatter(
            $options->getLocale(),
            $options->getDateType(),
            $options->getTimeType(),
            $options->getTimezone(),
            $options->getCalendar(),
            $pattern
        );
    }
}
