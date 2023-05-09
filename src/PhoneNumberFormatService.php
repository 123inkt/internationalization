<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions;
use InvalidArgumentException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use RuntimeException;

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
        $regionCode = $options?->getDefaultRegion() ?? $this->defaultOptions->getDefaultRegion();
        $format     = $options?->getFormat() ?? $this->defaultOptions->getFormat();
        if ($format === null) {
            throw new InvalidArgumentException('PhoneNumberOptions: unable to format phoneNumber without a given format');
        }

        $this->phoneNumberUtil ??= PhoneNumberUtil::getInstance();

        try {
            /** @var PhoneNumber $parsedNumber */
            $parsedNumber = $this->phoneNumberUtil->parse($phoneNumber, $regionCode);
        } catch (NumberParseException $e) {
            throw new RuntimeException("Unable to parse phoneNumber: " . $phoneNumber, 0, $e);
        }

        if ($format === PhoneNumberFormatOptions::FORMAT_INTERNATIONAL_DIAL) {
            $metaData = $this->phoneNumberUtil->getMetadataForRegion((string)$regionCode);
            $prefix   = $metaData?->getInternationalPrefix() ?? $metaData?->getPreferredInternationalPrefix();
            if (is_numeric($prefix)) {
                return $prefix . ltrim($this->phoneNumberUtil->format($parsedNumber, PhoneNumberFormatOptions::FORMAT_E164), '+');
            }

            return $this->phoneNumberUtil->format($parsedNumber, PhoneNumberFormatOptions::FORMAT_E164);
        }

        return $this->phoneNumberUtil->format($parsedNumber, $format);
    }
}
