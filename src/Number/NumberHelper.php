<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

class NumberHelper
{
    /**
     * detect if value has decimals:
     * cases:
     *    12.30 -  12 => abs(0.3)  => 0.3
     *   -12.30 - -12 => abs(-0.3) => 0.3
     */
    public static function hasDecimals(int|float $value): bool
    {
        if (is_int($value)) {
            return false;
        }

        // Due to floating point precision errors, rounding _must_ be applied to detect if a float has decimals.
        //
        // The first round is required because of: (int)0.999999999 => 0
        // The second round is to avoid having: 0.000000000000001 precision difference.
        return round(abs($value - round($value)), 5) > 0;
    }
}
