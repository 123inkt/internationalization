<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use InvalidArgumentException;
use NumberFormatter;

/**
 * @internal
 */
class NumberFormatterFactory
{
    private NumberFormatOptions $defaultOptions;

    public function __construct(NumberFormatOptions $defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    public function create(NumberFormatOptions $options): NumberFormatter
    {
        $locale = $options->getLocale() ?? $this->defaultOptions->getLocale();
        if ($locale === null) {
            throw new InvalidArgumentException('NumberFormatOptions: unable to format number without a locale');
        }

        return NumberFormatterFactoryHelper::applyNumberFormatOptions(
            $options,
            $this->defaultOptions,
            new NumberFormatter($locale, NumberFormatter::DECIMAL)
        );
    }
}
