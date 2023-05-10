<?php
declare(strict_types=1);


namespace DR\Internationalization\PhoneNumber;

use libphonenumber\PhoneNumberFormat;

/**
 * @phpstan-type Format self::FORMAT_*
 */
class PhoneNumberFormatOptions
{
    /**
     * Formats the NL phoneNumber "101234567" as "+31101234567"
     */
    public const FORMAT_E164 = PhoneNumberFormat::E164;

    /**
     * Formats the NL phoneNumber "101234567" as "+31 10 123 4567"
     */
    public const FORMAT_INTERNATIONAL = PhoneNumberFormat::INTERNATIONAL;

    /**
     * Formats the NL phoneNumber "101234567" as "010 123 4567"
     */
    public const FORMAT_NATIONAL = PhoneNumberFormat::NATIONAL;

    /**
     * Formats the NL phoneNumber "101234567" as "tel:+31-10-123-4567"
     */
    public const FORMAT_RFC3966 = PhoneNumberFormat::RFC3966;

    /**
     * Formats the NL phoneNumber "101234567" as "0031101234567"
     */
    public const FORMAT_INTERNATIONAL_DIAL = 4;

    private ?string $defaultCountryCode = null;
    private ?int $format = null;

    public function getDefaultCountryCode(): ?string
    {
        return $this->defaultCountryCode;
    }

    public function setDefaultCountryCode(?string $defaultCountryCode): self
    {
        $this->defaultCountryCode = $defaultCountryCode;

        return $this;
    }

    public function getFormat(): ?int
    {
        return $this->format;
    }

    /**
     * @phpstan-param Format $format
     */
    public function setFormat(?int $format): self
    {
        $this->format = $format;

        return $this;
    }
}
