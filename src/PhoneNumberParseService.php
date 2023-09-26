<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DR\Internationalization\PhoneNumber\PhoneNumber;
use DR\Internationalization\PhoneNumber\PhoneNumberTypeEnum;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberParseService
{
    private ?PhoneNumberUtil $phoneNumberUtil = null;

    public function __construct(private readonly string $countryCode)
    {
    }

    public function parse(string $phoneNumber): PhoneNumber
    {
        $this->phoneNumberUtil ??= PhoneNumberUtil::getInstance();

        try {
            $parsedNumber = $this->phoneNumberUtil->parse($phoneNumber, $this->countryCode, keepRawInput: true);
        } catch (NumberParseException $e) {
            throw new InvalidArgumentException("Unable to parse phoneNumber: " . $phoneNumber, 0, $e);
        }

        if ($this->phoneNumberUtil->isValidNumber($parsedNumber) === false) {
            throw new InvalidArgumentException("Number is invalid: " . $phoneNumber);
        }

        $metaData = $this->phoneNumberUtil->getMetadataForRegion($this->countryCode);
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
            $this->getNumberType($this->phoneNumberUtil->getNumberType($parsedNumber)),
            $parsedNumber,
            $parsedNumber->getExtension()
        );
    }

    private function getNumberType(int $numberType): PhoneNumberTypeEnum
    {
        $numberType = PhoneNumberType::values()[$numberType];

        return PhoneNumberTypeEnum::tryFrom($numberType) ?? PhoneNumberTypeEnum::UNKNOWN;
    }
}
