<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

enum NumberFormatTrimDecimalsEnum: int
{
    /**
     * Do not trim any trailing zero decimals.
     */
    case NONE = 1;

    /**
     * Trim decimals only if all of them can be removed.
     * 12.34 => 12.34
     * 12.30 => 12.30
     * 12.00 => 12
     */
    case ALL_OR_NOTHING = 2;

    /**
     * Trim any trailing decimal zero's.
     * 12.34 => 12.34
     * 12.30 => 12.3
     * 12.00 => 12
     */
    case ANY = 3;
}
