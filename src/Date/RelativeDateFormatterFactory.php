<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

class RelativeDateFormatterFactory
{
    public function create(string $locale): IntlDateFormatter
    {
        return new IntlDateFormatter(
            $locale,
            IntlDateFormatter::RELATIVE_FULL,
            IntlDateFormatter::NONE,
            'UTC',
            IntlDateFormatter::GREGORIAN
        );
    }
}
