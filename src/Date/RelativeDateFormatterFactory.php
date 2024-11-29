<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

/**
 * @internal
 */
class RelativeDateFormatterFactory
{
    public function createRelativeFull(DateFormatOptions $options): IntlDateFormatter
    {
        return new IntlDateFormatter(
            $options->getLocale(),
            IntlDateFormatter::RELATIVE_FULL,
            IntlDateFormatter::NONE,
            $options->getTimezone(),
            IntlDateFormatter::GREGORIAN,
        );
    }

    public function createFull(DateFormatOptions $options): IntlDateFormatter
    {
        return new IntlDateFormatter(
            $options->getLocale(),
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            $options->getTimezone(),
            IntlDateFormatter::GREGORIAN
        );
    }
}
