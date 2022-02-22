<?php
declare(strict_types=1);

namespace DR\Internationalization\Currency;

use DR\Internationalization\Number\NumberFormatOptions;

class CurrencyFormatOptions extends NumberFormatOptions
{
    protected ?bool   $symbol       = null;
    protected ?string $currencyCode = null;

    public function hasSymbol(): ?bool
    {
        return $this->symbol;
    }

    /**
     * Show/hide the formatting symbol. If `null` defaults to system default style. Defaults to locale configuration.
     */
    public function setSymbol(?bool $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    /**
     * Set the ISO 4217 currency code. Defaults to system or to locale configuration.
     */
    public function setCurrencyCode(?string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function __toString(): string
    {
        return "currency:" . serialize(['currency' => $this->currencyCode, 'symbol' => $this->symbol]) . parent::__toString();
    }
}
