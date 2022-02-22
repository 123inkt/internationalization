<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use DR\Internationalization\Number\NumberFormatterSplitterResult as Result;
use InvalidArgumentException;
use NumberFormatter;

/**
 * @internal
 */
class NumberFormatterSplitter
{
    private NumberFormatter $formatter;
    private ?int            $symbolFlag;
    private int             $groupingFlag;
    private int             $decimalFlag;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(NumberFormatter $formatter, int $formatterStyle)
    {
        $this->formatter = $formatter;
        if ($formatterStyle === NumberFormatter::DECIMAL) {
            $this->symbolFlag   = null;
            $this->groupingFlag = NumberFormatter::GROUPING_SEPARATOR_SYMBOL;
            $this->decimalFlag  = NumberFormatter::DECIMAL_SEPARATOR_SYMBOL;
        } elseif ($formatterStyle === NumberFormatter::CURRENCY) {
            $this->symbolFlag   = NumberFormatter::CURRENCY_SYMBOL;
            $this->groupingFlag = NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL;
            $this->decimalFlag  = NumberFormatter::MONETARY_SEPARATOR_SYMBOL;
        } else {
            throw new InvalidArgumentException("NumberFormatter type: " . $formatterStyle . " is not supported (yet)");
        }
    }

    /**
     * A method to format the given value according to the provided NumberFormatter and have the result be separated into individual parts:
     * symbol position, prefix, suffix, integer, decimals, and decimal separator
     */
    public function split(string $formattedValue, bool $isPositiveValue): NumberFormatterSplitterResult
    {
        $symbol         = $this->symbolFlag === null ? null : $this->formatter->getSymbol($this->symbolFlag);
        $groupSeparator = $this->formatter->getSymbol($this->groupingFlag);
        $decSeparator   = $this->formatter->getSymbol($this->decimalFlag);

        // fetch value prefix and suffix
        if ($isPositiveValue) {
            $prefix = $this->formatter->getTextAttribute(NumberFormatter::POSITIVE_PREFIX);
            $suffix = $this->formatter->getTextAttribute(NumberFormatter::POSITIVE_SUFFIX);
        } else {
            $prefix = $this->formatter->getTextAttribute(NumberFormatter::NEGATIVE_PREFIX);
            $suffix = $this->formatter->getTextAttribute(NumberFormatter::NEGATIVE_SUFFIX);
        }

        // determine symbol position
        if ($symbol === null || $symbol === '') {
            $symbol   = null;
            $position = Result::POSITION_ABSENT;
        } elseif (stripos($prefix, $symbol) !== false) {
            $position = Result::POSITION_BEFORE;
        } elseif (stripos($suffix, $symbol) !== false) {
            $position = Result::POSITION_AFTER;
        } else {
            $symbol   = null;
            $position = Result::POSITION_ABSENT;
        }

        // strip prefix and suffix, and split on decimal separator
        $numericValue = str_replace([$prefix, $suffix], '', $formattedValue);
        if ($decSeparator !== '' && strpos($numericValue, $decSeparator) !== false) {
            [$integer, $decimals] = explode($decSeparator, $numericValue, 2);
        } else {
            $integer  = $numericValue;
            $decimals = '';
        }

        return new Result($formattedValue, $prefix, $suffix, $integer, $groupSeparator, $decimals, $decSeparator, $symbol, $position);
    }
}
