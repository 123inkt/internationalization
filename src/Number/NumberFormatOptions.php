<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use NumberFormatter;

class NumberFormatOptions
{
    /**
     * Do not trim any trailing zero decimals.
     */
    public const TRIM_DECIMAL_NONE = 1;

    /**
     * Trim decimals only if all of them can be removed.
     * 12.34 => 12.34
     * 12.30 => 12.30
     * 12.00 => 12
     */
    public const TRIM_DECIMAL_ALL_OR_NOTHING = 2;

    /**
     * Trim any trailing decimal zero's.
     * 12.34 => 12.34
     * 12.30 => 12.3
     * 12.00 => 12
     */
    public const TRIM_DECIMAL_ANY = 3;

    protected ?string $locale       = null;
    protected ?int    $decimals     = null;
    protected ?bool   $grouping     = null;
    /** @phpstan-var self::TRIM_DECIMAL_*|null  */
    protected ?int    $trimDecimals = null;
    protected ?int    $rounding     = null;

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

    /**
     * @phpstan-return self::TRIM_DECIMAL_*|null
     */
    public function getTrimDecimals(): ?int
    {
        return $this->trimDecimals;
    }

    /**
     * Trim the trailing decimals. If no decimals are left, the decimal separator will also be trimmed.
     * Defaults to `TRIM_DECIMAL_ANY` for number formatting, and `TRIM_DECIMAL_NONE` for currencies.
     * @phpstan-param self::TRIM_DECIMAL_*|null $trimDecimals
     * @return static
     */
    public function setTrimDecimals(?int $trimDecimals): self
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
