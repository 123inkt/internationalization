<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use function preg_match;
use function preg_quote;
use function preg_replace;
use function round;
use function str_replace;
use function strpos;
use function substr_count;

class NumberParser
{
    /**
     * Parse a string to a float. Autodetect which thousand/decimal separator is used
     *
     * @return false|float float for valid float number, or false otherwise
     */
    public static function parseFloat(string $value, ?int $decimals = null): float|bool
    {
        $value = (string)preg_replace("/(\s|\xc2\xa0|\xE2\x80\xAF)+/", '', $value);

        // empty or strange characters will return false
        if ($value === '' || preg_match('/[^\d.,-]/', $value) === 1) {
            return false;
        }

        // When we dont have a single separator just parse the value
        if (str_contains($value, ',') === false && str_contains($value, '.') === false) {
            $result = (float)$value;
            if ($decimals !== null) {
                $result = round((float)$value, $decimals);
            }

            return $result;
        }

        $numberSeparators  = self::determineSeparators($value);
        $decimalSeparator  = $numberSeparators->getDecimal();
        $thousandSeparator = $numberSeparators->getThousand();
        if ($decimalSeparator !== null && substr_count($value, $decimalSeparator) > 1) {
            return false; // Decimal separator occurs more than one time in the value
        }

        if ($thousandSeparator !== null) {
            $pattern = '^[-]?\\d{1,3}(' . preg_quote($thousandSeparator, '/') . '\\d{3})*';
            if ($decimalSeparator !== null) {
                $pattern .= preg_quote($decimalSeparator, '/') . '\\d*';
            }
            $pattern .= '$';

            if (preg_match('/' . $pattern . '/', $value) !== 1) {
                return false;
            }

            // Replace the thousands separator with empty string so that we can pass cast to float
            $value = str_replace($thousandSeparator, '', $value);
        }

        // Replace the decimal separator with a dot if it isn't already a dot so that we can cast it to float
        if ($decimalSeparator !== null && $decimalSeparator !== '.') {
            $value = str_replace($decimalSeparator, '.', $value);
        }

        $result = (float)$value;
        if ($decimals !== null) {
            $result = round($result, $decimals);
        }

        return $result;
    }

    /**
     * Determine the separators based on the position and occurrences of , and .
     */
    private static function determineSeparators(string $value): NumberSeparator
    {
        $indexOfComma = strpos($value, ',');
        $indexOfDot   = strpos($value, '.');

        if ($indexOfComma === false && $indexOfDot === false) {
            // @codeCoverageIgnoreStart
            return new NumberSeparator(null, null);
            // @codeCoverageIgnoreEnd
        }

        if ($indexOfComma !== false && $indexOfDot !== false) {
            return $indexOfComma > $indexOfDot ? new NumberSeparator(',', '.') : new NumberSeparator('.', ',');
        }

        $decimalSeparator  = null;
        $thousandSeparator = null;
        if ($indexOfComma !== false) {
            $decimalSeparator = ',';
        } elseif ($indexOfDot !== false) {
            $decimalSeparator = '.';
        }

        // if we find the previously determined decimal separator multiple times and we determined that there is no thousand separator then the
        // value will be parsed with the previously determined decimal separator as the thousand separator
        if ($decimalSeparator !== null && substr_count($value, $decimalSeparator) > 1) {
            return new NumberSeparator($thousandSeparator, $decimalSeparator);
        }

        return new NumberSeparator($decimalSeparator, $thousandSeparator);
    }
}
