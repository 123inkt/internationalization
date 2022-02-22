<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Number\NumberFormatOptions as Options;
use Money\Currencies;
use Money\Currency;
use Money\Money;

/**
 * Hiding empty decimals isn't possible by default configuration via \NumberFormatter. Instead we're sniffing the value has decimals, and if there
 * are absolutely no decimals, the $options->setDecimals will be set to 0 to force removing them all.
 * @internal
 */
class NumberFormatOptionsHelper
{
    private CurrencyFormatOptions $defaultCurrencyOptions;
    private Options               $defaultNumberOptions;
    /** @phpstan-var Currencies<Currency[]> */
    private Currencies $currencies;

    /**
     * @phpstan-param Currencies<Currency[]> $currencies
     */
    public function __construct(CurrencyFormatOptions $defaultCurrencyOptions, Options $defaultNumberOptions, Currencies $currencies)
    {
        $this->defaultCurrencyOptions = $defaultCurrencyOptions;
        $this->defaultNumberOptions   = $defaultNumberOptions;
        $this->currencies             = $currencies;
    }

    /**
     * @param int|float|Money $value
     */
    public function applyCurrencyOptions($value, ?CurrencyFormatOptions $options): CurrencyFormatOptions
    {
        $decimals = ($options !== null ? $options->getDecimals() : null) ?? $this->defaultNumberOptions->getDecimals();

        if ($decimals === 0 || self::hideDecimals($this->defaultCurrencyOptions, $options) === false || $this->currencyHasDecimals($value)) {
            return $options ?? $this->defaultCurrencyOptions;
        }

        // clone options to avoid modifying the externally passed object
        $options = $options === null ? new CurrencyFormatOptions() : clone $options;
        $options->setDecimals(0);

        return $options;
    }

    /**
     * @param int|float $value
     */
    public function applyNumberOptions($value, ?Options $options): Options
    {
        $decimals = ($options !== null ? $options->getDecimals() : null) ?? $this->defaultNumberOptions->getDecimals();

        // if decimal is set, and value is integer, and we should hide decimals
        if ($decimals === null ||
            $decimals === 0 ||
            self::hideDecimals($this->defaultNumberOptions, $options) === false ||
            NumberHelper::hasDecimals($value)
        ) {
            return $options ?? $this->defaultNumberOptions;
        }

        // clone options to avoid modifying the externally passed object
        $options = $options === null ? new Options() : clone $options;
        $options->setDecimals(0);

        return $options;
    }

    /**
     * @param int|float|Money $value
     */
    private function currencyHasDecimals($value): bool
    {
        if ($value instanceof Money) {
            $subunit = $this->currencies->subunitFor($value->getCurrency()) * 10;

            return $value->getAmount() % $subunit !== 0;
        }

        return NumberHelper::hasDecimals($value);
    }

    private static function hideDecimals(Options $defaultOptions, ?Options $options): bool
    {
        $value = ($options !== null ? $options->getTrimDecimals() : null) ?? $defaultOptions->getTrimDecimals();

        return $value === Options::TRIM_DECIMAL_ALL_OR_NOTHING;
    }
}
