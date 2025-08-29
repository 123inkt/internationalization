<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use NumberFormatter;

class NumberFormatOptions
{
    protected ?string                       $locale       = null;
    protected ?int                          $decimals     = null;
    protected ?bool                         $grouping     = null;
    protected ?NumberFormatTrimDecimalsEnum $trimDecimals = null;
    protected ?int                          $rounding     = null;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Set the preferred locale for the formatting. Expects an ietf code (nl_NL, nl_BE, en_GB, etc...). Defaults to system configuration.
     * @return static
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getDecimals(): ?int
    {
        return $this->decimals;
    }

    /**
     * Set the amount of decimals should be formatted to. Defaults to system or locale configuration.
     * @return static
     */
    public function setDecimals(?int $decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    public function isGrouping(): ?bool
    {
        return $this->grouping;
    }

    /**
     * Show/hide thousands separator. If null defaults to system or locale configuration.
     * @return static
     */
    public function setGrouping(?bool $grouping): self
    {
        $this->grouping = $grouping;

        return $this;
    }

    public function getTrimDecimals(): ?NumberFormatTrimDecimalsEnum
    {
        return $this->trimDecimals;
    }

    /**
     * Trim the trailing decimals. If no decimals are left, the decimal separator will also be trimmed.
     * Defaults to `NumberFormatTrimDecimalsEnum::ANY` for number formatting, and `NumberFormatTrimDecimalsEnum::NONE` for currencies.
     * @return static
     */
    public function setTrimDecimals(?NumberFormatTrimDecimalsEnum $trimDecimals): self
    {
        $this->trimDecimals = $trimDecimals;

        return $this;
    }

    /**
     * Method to round the numbers. Defaults to system default or NumberFormatter::ROUND_HALFUP
     * - NumberFormatter::ROUND_HALFUP
     * - NumberFormatter::ROUND_CEILING
     * - NumberFormatter::ROUND_FLOOR
     * - NumberFormatter::ROUND_DOWN
     * - NumberFormatter::ROUND_HALFEVEN
     * - NumberFormatter::ROUND_HALFDOWN
     * - NumberFormatter::ROUND_HALFUP
     * @phpstan-param NumberFormatter::ROUND_* $rounding
     * @see  NumberFormatter
     * @link https://www.php.net/manual/en/class.numberformatter.php#numberformatter.constants.round-ceiling
     */
    public function setRounding(int $rounding): self
    {
        $this->rounding = $rounding;

        return $this;
    }

    public function getRounding(): ?int
    {
        return $this->rounding;
    }

    public function __toString(): string
    {
        return "number:" . serialize(
            [
                'locale'   => $this->locale,
                'grouping' => $this->grouping,
                'decimals' => $this->decimals,
                'trim'     => $this->trimDecimals,
                'rounding' => $this->rounding
            ]
        );
    }
}
