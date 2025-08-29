<?php
declare(strict_types=1);

namespace DR\Internationalization\PhoneNumber;

class PhoneNumberFormatOptions
{
    private ?string                $defaultCountryCode = null;
    private ?PhoneNumberFormatEnum $format             = null;

    public function getDefaultCountryCode(): ?string
    {
        return $this->defaultCountryCode;
    }

    public function setDefaultCountryCode(?string $defaultCountryCode): self
    {
        $this->defaultCountryCode = $defaultCountryCode;

        return $this;
    }

    public function getFormat(): ?PhoneNumberFormatEnum
    {
        return $this->format;
    }

    public function setFormat(?PhoneNumberFormatEnum $format): self
    {
        $this->format = $format;

        return $this;
    }
}
