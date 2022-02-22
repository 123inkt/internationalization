<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Currency\CurrencyFormatterFactory;
use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatOptionsHelper;
use DR\Internationalization\Number\NumberFormatterCache;
use DR\Internationalization\Number\NumberFormatterCacheInterface;
use DR\Internationalization\Number\NumberFormatterFactory;
use DR\Internationalization\Number\NumberFormatterSplitter;
use DR\Internationalization\Number\NumberFormatterSplitterResult;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

class NumberFormatService
{
    private CurrencyFormatterFactory      $currencyFactory;
    private NumberFormatterFactory        $numberFactory;
    private NumberFormatterCacheInterface $cache;
    private NumberFormatOptionsHelper     $optionsHelper;

    /** @phpstan-var Currencies<Currency[]> */
    private Currencies $currencies;

    /**
     * @phpstan-param Currencies<Currency[]>|null $currencies
     */
    public function __construct(
        CurrencyFormatOptions $currencyFormatOptions,
        NumberFormatOptions $numberFormatOptions,
        ?Currencies $currencies = null,
        ?NumberFormatterCacheInterface $cache = null
    ) {
        $this->currencies      = $currencies ?? new ISOCurrencies();
        $this->currencyFactory = new CurrencyFormatterFactory($currencyFormatOptions);
        $this->numberFactory   = new NumberFormatterFactory($numberFormatOptions);
        $this->optionsHelper   = new NumberFormatOptionsHelper($currencyFormatOptions, $numberFormatOptions, $this->currencies);
        $this->cache           = $cache ?? new NumberFormatterCache();
    }

    public function currency(int|float|Money $value, ?CurrencyFormatOptions $options = null): string
    {
        if ($value instanceof Money && $options !== null && $options->getCurrencyCode() !== null) {
            throw new InvalidArgumentException('Can\'t specify custom currency code for Money objects');
        }

        $options = $this->optionsHelper->applyCurrencyOptions($value, $options);

        return $this->formatCurrencyValue($value, $this->getCurrencyFormatter($options));
    }

    /**
     * Format a currency value and return the value split in it individual parts:
     * - prefix, suffix, symbol, decimal and thousands separator, integer and decimals value.
     */
    public function currencySplit(int|float|Money $value, ?CurrencyFormatOptions $options = null): NumberFormatterSplitterResult
    {
        if ($value instanceof Money && $options !== null && $options->getCurrencyCode() !== null) {
            throw new InvalidArgumentException('Can\'t specify custom currency code for Money objects');
        }

        $options         = $this->optionsHelper->applyCurrencyOptions($value, $options);
        $formatter       = $this->getCurrencyFormatter($options);
        $formattedValue  = $this->formatCurrencyValue($value, $formatter);
        $isPositiveValue = $value instanceof Money ? $value->getAmount() >= 0 : $value >= 0;

        return (new NumberFormatterSplitter($formatter, NumberFormatter::CURRENCY))->split($formattedValue, $isPositiveValue);
    }

    public function number(int|float $value, ?NumberFormatOptions $options = null): string
    {
        if ($value === -0.0) {
            $value = 0;
        }

        $options = $this->optionsHelper->applyNumberOptions($value, $options);

        return $this->getNumberFormatter($options)->format($value);
    }

    /**
     * Format a numeric value and return the value split in it individual parts:
     * - prefix, suffix, decimal and thousands separator, integer and decimals value.
     */
    public function numberSplit(int|float $value, ?NumberFormatOptions $options = null): NumberFormatterSplitterResult
    {
        if ($value === -0.0) {
            $value = 0;
        }

        $options        = $this->optionsHelper->applyNumberOptions($value, $options);
        $formatter      = $this->getNumberFormatter($options);
        $formattedValue = $formatter->format($value);

        return (new NumberFormatterSplitter($formatter, NumberFormatter::DECIMAL))->split($formattedValue, $value >= 0);
    }

    private function formatCurrencyValue(int|float|Money $value, NumberFormatter $formatter): string
    {
        // format according to Locale and Currency
        if ($value instanceof Money) {
            return (new IntlMoneyFormatter($formatter, $this->currencies))->format($value);
        }

        return $formatter->format($value);
    }

    private function getCurrencyFormatter(CurrencyFormatOptions $options): NumberFormatter
    {
        // get or create from cache
        return $this->cache->get((string)$options, fn() => $this->currencyFactory->create($options));
    }

    private function getNumberFormatter(NumberFormatOptions $options): NumberFormatter
    {
        // get or create from cache
        return $this->cache->get((string)$options, fn() => $this->numberFactory->create($options));
    }
}
