<?php
declare(strict_types=1);

namespace DR\Internationalization\PhoneNumber;

use libphonenumber\PhoneNumber as LibPhoneNumber;

class PhoneNumber
{
    /**
     * @param string $countryCode 2 Character ISO country code (NL, BE, FR, ES)
     *
     * @internal Only receive this object from the PhoneNumberParseService::parse function.
     */
    public function __construct(
        private readonly string $internationalDialCode,
        private readonly string $countryDialCode,
        private readonly string $nationalNumber,
        private readonly string $rawInput,
        private readonly string $countryCode,
        private readonly PhoneNumberTypeEnum $numberType,
        private readonly LibPhoneNumber $phoneNumber,
        private readonly ?string $extension = null
    ) {
    }

    /**
     * Contains the international dail code (IDD) for the phonenumber. Which is for the majority of the world 00 or 011.
     * Examples: 00 (Europe, Middle East, North America, South America)
     *           0011 (Australia)
     *           011 (Canada, Jamaica, Sint Maarten),
     *           8~10 (Belarus, Uzbekistan, Tajikistan)
     */
    public function getInternationalDialCode(): string
    {
        return $this->internationalDialCode;
    }

    /**
     * Country calling codes, country dial-in codes, international subscriber dialing (ISD) codes, or most commonly, telephone country codes
     * Examples: 31 Netherlands, 32 Belgium, 46, Sweden
     */
    public function getCountryDialCode(): string
    {
        return $this->countryDialCode;
    }

    /**
     * National (significant) Number is a language/country-neutral representation of a phone number at a country level.
     * Examples:    0612345678 -> 612345678
     *              +31 6 12345678 -> 612345678
     */
    public function getNationalNumber(): string
    {
        return $this->nationalNumber;
    }

    /**
     * Extension is not standardized in ITU recommendations, except for being defined as a series of numbers with a maximum length of 40 digits. It
     * is defined as a string here to accommodate for the possible use of a leading zero in the extension (organizations have complete freedom to do
     * so, as there is no standard defined)
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * The raw input that the function has to parse.
     * Examples:    Input:      0612345678
     *              rawInput:   0612345678
     *              Input:      +1-202-555-0142
     *              rawInput:   +1-202-555-0142
     */
    public function getRawInput(): string
    {
        return $this->rawInput;
    }

    /**
     * 2 Character ISO country code
     * Examples: NL, BE, FR, ES
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * Phone numbers can be of a specific type. This functions returns the type of the number in the form of a enum.
     * Examples:    FIXED_LINE, MOBILE, VOIP, STANDARD_RATE
     */
    public function getNumberType(): PhoneNumberTypeEnum
    {
        return $this->numberType;
    }

    /**
     * @internal
     */
    public function getPhoneNumber(): LibPhoneNumber
    {
        return $this->phoneNumber;
    }
}
