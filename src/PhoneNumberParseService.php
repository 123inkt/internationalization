<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DR\Internationalization\PhoneNumber\PhoneNumber;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberParseService
{
    private ?PhoneNumberUtil $phoneNumberUtil = null;

    public function __construct(private readonly string $defaultCountryCode)
    {
    }

    public function parse(string $phoneNumber, ?string $countryCode = null): PhoneNumber
    {
        $this->phoneNumberUtil ??= PhoneNumberUtil::getInstance();

        $countryCode ??= $this->defaultCountryCode;

        try {
            $parsedNumber = $this->phoneNumberUtil->parse($phoneNumber, $countryCode, null, true);
        } catch (NumberParseException $e) {
            throw new InvalidArgumentException("Unable to parse phoneNumber: " . $phoneNumber, 0, $e);
        }

        if ($this->phoneNumberUtil->isValidNumber($parsedNumber) === false) {
            throw new InvalidArgumentException("Number is invalid: " . $phoneNumber);
        }

        $metaData = $this->phoneNumberUtil->getMetadataForRegion($countryCode);
        $prefix   = $metaData?->getInternationalPrefix() ?? $metaData?->getPreferredInternationalPrefix();

        if (is_numeric($prefix) === false) {
            $prefix = '';
        }

        // We parse a couple of values to string. The values cannot be null because it's a valid number
        return new PhoneNumber(
            $prefix,
            (string)$parsedNumber->getCountryCode(),
            (string)$parsedNumber->getNationalNumber(),
            (string)$parsedNumber->getRawInput(),
            (string)$this->phoneNumberUtil->getRegionCodeForNumber($parsedNumber),
            $this->phoneNumberUtil->getNumberType($parsedNumber),
            $parsedNumber,
            $parsedNumber->getExtension()
        );
    }
}
