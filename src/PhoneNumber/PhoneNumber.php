<?php
declare(strict_types=1);

namespace DR\Internationalization\PhoneNumber;

use libphonenumber\PhoneNumber as LibPhoneNumber;

class PhoneNumber
{
    /**
     * @param string $countryCode 2 Character ISO country code (NL, BE, FR, ES)
     *
     * @interal LibPhoneNumber $phoneNumber
     */
    public function __construct(
        private string $internationalDialCode,
        private string $countryDialCode,
        private string $nationalNumber,
        private string $rawInput,
        private string $countryCode,
        private PhoneNumberTypeEnum $numberType,
        private LibPhoneNumber $phoneNumber,
        private ?string $extension = null
    ) {
    }

    public function getInternationalDialCode(): string
    {
        return $this->internationalDialCode;
    }

    public function getCountryDialCode(): string
    {
        return $this->countryDialCode;
    }

    public function getNationalNumber(): string
    {
        return $this->nationalNumber;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function getRawInput(): string
    {
        return $this->rawInput;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getNumberType(): PhoneNumberTypeEnum
    {
        return $this->numberType;
    }

    public function getPhoneNumber(): LibPhoneNumber
    {
        return $this->phoneNumber;
    }
}
