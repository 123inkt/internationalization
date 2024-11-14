<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

/**
 * Hiding empty decimals isn't possible by default configuration via \NumberFormatter. Instead we're sniffing the value has decimals, and if there
 * are absolutely no decimals, the $options->setDecimals will be set to 0 to force removing them all.
 * @internal
 */
class DateFormatOptionsHelper
{
    public function __construct(private DateFormatOptions $options)
    {
    }

    public function getOptions(): DateFormatOptions
    {
        return $this->options;
    }

    public function setOptions(DateFormatOptions $options): DateFormatOptionsHelper
    {
        $this->options = clone $options;

        return $this;
    }
}
