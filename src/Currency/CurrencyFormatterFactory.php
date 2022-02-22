<?php
declare(strict_types=1);

namespace DR\Internationalization\Currency;

use DR\Internationalization\Number\NumberFormatterFactoryHelper;
use InvalidArgumentException;
use NumberFormatter;

/**
 * @internal
 */
class CurrencyFormatterFactory
{
    private CurrencyFormatOptions $defaultOptions;

    public function __construct(CurrencyFormatOptions $defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    public function create(CurrencyFormatOptions $options): NumberFormatter
    {
        $locale = $options->getLocale() ?? $this->defaultOptions->getLocale();
        if ($locale === null) {
            throw new InvalidArgumentException('CurrencyFormatOptions: unable to format currency without a locale');
        }

        $currencyCode = $options->getCurrencyCode() ?? $this->defaultOptions->getCurrencyCode();
        if ($currencyCode !== null) {
            // set specific currency code
            $locale .= "@currency=" . strtoupper($currencyCode);
        }

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatter = $this->applyCurrencyFormatOptions($options, $formatter);

        return NumberFormatterFactoryHelper::applyNumberFormatOptions($options, $this->defaultOptions, $formatter);
    }

    private function applyCurrencyFormatOptions(CurrencyFormatOptions $options, NumberFormatter $formatter): NumberFormatter
    {
        $symbol = $options->hasSymbol() ?? $this->defaultOptions->hasSymbol();

        // hide symbol
        if ($symbol === false) {
            $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
            // remove any spacing for positive numbers
            $formatter->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, '');
            $formatter->setTextAttribute(NumberFormatter::POSITIVE_SUFFIX, '');

            // remove any spacing for negative numbers
            $spaces = "\xC2\xA0 "; // nbsp + space
            $formatter->setTextAttribute(
                NumberFormatter::NEGATIVE_PREFIX,
                ltrim($formatter->getTextAttribute(NumberFormatter::NEGATIVE_PREFIX), $spaces)
            );
            $formatter->setTextAttribute(
                NumberFormatter::NEGATIVE_SUFFIX,
                rtrim($formatter->getTextAttribute(NumberFormatter::NEGATIVE_SUFFIX), $spaces)
            );
        }

        return $formatter;
    }
}
