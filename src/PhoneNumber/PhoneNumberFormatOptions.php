<?php
declare(strict_types=1);


namespace DR\Internationalization\PhoneNumber;

use libphonenumber\PhoneNumberFormat;

/**
 * @phpstan-type Format self::FORMAT_*
 */
class PhoneNumberFormatOptions
{
    public const FORMAT_E164 = PhoneNumberFormat::E164;
    public const FORMAT_INTERNATIONAL = PhoneNumberFormat::INTERNATIONAL;
    public const FORMAT_NATIONAL = PhoneNumberFormat::NATIONAL;
    public const FORMAT_RFC3966 = PhoneNumberFormat::RFC3966;
    public const FORMAT_INTERNATIONAL_DIAL = 4;

    private ?string $defaultRegion = null;
    private ?int $format = null;

    public function getDefaultRegion(): ?string
    {
        return $this->defaultRegion;
    }

    public function setDefaultRegion(?string $defaultRegion): self
    {
        $this->defaultRegion = $defaultRegion;

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
