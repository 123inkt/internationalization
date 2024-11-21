<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

/**
 * @phpstan-type DateFormatType self::*
 */
class DateFormatTypes
{
    public const NONE = IntlDateFormatter::NONE;
    public const FULL = IntlDateFormatter::FULL;
    public const LONG = IntlDateFormatter::LONG;
    public const MEDIUM = IntlDateFormatter::MEDIUM;
    public const SHORT = IntlDateFormatter::SHORT;
    public const RELATIVE_FULL = IntlDateFormatter::RELATIVE_FULL;
    public const RELATIVE_LONG = IntlDateFormatter::RELATIVE_LONG;
    public const RELATIVE_MEDIUM = IntlDateFormatter::RELATIVE_MEDIUM;
    public const RELATIVE_SHORT = IntlDateFormatter::RELATIVE_SHORT;
}
