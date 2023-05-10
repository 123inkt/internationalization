<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberFormatService
{
    private PhoneNumberFormatOptions $defaultOptions;
    private ?PhoneNumberUtil $phoneNumberUtil = null;

    public function __construct(PhoneNumberFormatOptions $phoneNumberOptions)
    {
        $this->defaultOptions = $phoneNumberOptions;
    }

    public function format(string $phoneNumber, PhoneNumberFormatOptions $options = null): string
    {
        $countryCode = $options?->getDefaultCountryCode() ?? $this->defaultOptions->getDefaultCountryCode();
        $format      = $options?->getFormat() ?? $this->defaultOptions->getFormat();
        if ($format === null) {
            throw new InvalidArgumentException('PhoneNumberOptions: unable to format phoneNumber without a given format');
        }

        $this->phoneNumberUtil ??= PhoneNumberUtil::getInstance();

        try {
            $parsedNumber = $this->phoneNumberUtil->parse($phoneNumber, $countryCode);
        } catch (NumberParseException $e) {
            throw new InvalidArgumentException("Unable to parse phoneNumber: " . $phoneNumber, 0, $e);
        }

        if ($format === PhoneNumberFormatOptions::FORMAT_INTERNATIONAL_DIAL) {
            $metaData = $this->phoneNumberUtil->getMetadataForRegion((string)$countryCode);
            $prefix   = $metaData?->getInternationalPrefix() ?? $metaData?->getPreferredInternationalPrefix();
            if (is_numeric($prefix)) {
                return $prefix . ltrim($this->phoneNumberUtil->format($parsedNumber, PhoneNumberFormatOptions::FORMAT_E164), '+');
            }

            return $this->phoneNumberUtil->format($parsedNumber, PhoneNumberFormatOptions::FORMAT_E164);
        }

        return $this->phoneNumberUtil->format($parsedNumber, $format);
    }
}
