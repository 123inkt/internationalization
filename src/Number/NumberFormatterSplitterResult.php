<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

class NumberFormatterSplitterResult
{
    public const POSITION_ABSENT = 'absent';
    public const POSITION_BEFORE = 'before';
    public const POSITION_AFTER  = 'after';

    private string  $value;
    private string  $prefix;
    private string  $suffix;
    private string  $integer;
    private string  $groupingSeparator;
    private string  $decimals;
    private string  $decimalSeparator;
    private ?string $symbol;
    private string  $symbolPosition;

    public function __construct(
        string $value,
        string $prefix,
        string $suffix,
        string $integer,
        string $groupingSeparator,
        string $decimals,
        string $decimalSeparator,
        ?string $symbol,
        string $symbolPosition
    ) {
        $this->value             = $value;
        $this->prefix            = $prefix;
        $this->suffix            = $suffix;
        $this->groupingSeparator = $groupingSeparator;
        $this->integer           = $integer;
        $this->decimals          = $decimals;
        $this->decimalSeparator  = $decimalSeparator;
        $this->symbol            = $symbol;
        $this->symbolPosition    = $symbolPosition;
    }

    /**
     * The fully formatted value according to the NumberFormatter
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the formatted value without prefix nor suffix. Note the negative symbol is part of the prefix or suffix, because for some
     * locales when formatting currencies the format is: '-€1234.56'
     */
    public function getNumberValue(): string
    {
        return sprintf(
            '%s%s%s',
            $this->integer,
            $this->decimals !== '' ? $this->decimalSeparator : '',
            $this->decimals
        );
    }

    /**
     * The value prefix. This can contain the symbol and/or the negative symbol
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * The value suffix. This can contain the symbol and/or the negative symbol
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * All the numbers left of the decimal separator. Including the grouping (thousand) separator.
     */
    public function getInteger(): string
    {
        return $this->integer;
    }

    /**
     * The character(s) that represent the grouping separator according to the NumberFormatter
     */
    public function getGroupingSeparator(): string
    {
        return $this->groupingSeparator;
    }

    /**
     * All the numbers to the right of the decimal separator. Not including the decimal separator itself.
     * Will be empty string if the value was formatted without decimals.
     */
    public function getDecimals(): string
    {
        return $this->decimals;
    }

    /**
     * The decimal separator according to the NumberFormatter.
     */
    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * The symbol according to the Locale and formatting style. Will be null if the value was formatted without a symbol.
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * The symbol position. See constants. Values can be 'before', 'after', 'absent'.
     * Is `absent` when the value was formatted without a symbol.
     */
    public function getSymbolPosition(): string
    {
        return $this->symbolPosition;
    }
}
