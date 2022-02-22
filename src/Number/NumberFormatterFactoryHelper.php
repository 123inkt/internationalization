<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use NumberFormatter;

/**
 * @internal
 */
class NumberFormatterFactoryHelper
{
    public static function applyNumberFormatOptions(
        NumberFormatOptions $options,
        NumberFormatOptions $defaultOptions,
        NumberFormatter $formatter
    ): NumberFormatter {
        $grouping     = $options->isGrouping() ?? $defaultOptions->isGrouping();
        $decimals     = $options->getDecimals() ?? $defaultOptions->getDecimals();
        $trimDecimals = $options->getTrimDecimals() ?? $defaultOptions->getTrimDecimals() ?? NumberFormatOptions::TRIM_DECIMAL_ANY;
        $rounding     = $options->getRounding() ?? $defaultOptions->getRounding() ?? NumberFormatter::ROUND_HALFUP;

        // setup decimal round mode
        $formatter->setAttribute(NumberFormatter::ROUNDING_MODE, $rounding);

        // enable/disable grouping for decimals
        if ($grouping !== null) {
            $formatter->setAttribute(NumberFormatter::GROUPING_USED, (int)$grouping);
        }

        // setup decimal behaviour
        if ($decimals !== null) {
            if ($trimDecimals === NumberFormatOptions::TRIM_DECIMAL_ANY) {
                $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
            } else {
                $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimals);
            }
        }

        return $formatter;
    }
}
