<?php

declare(strict_types=1);

namespace DR\Internationalization\PhoneNumber;

use libphonenumber\PhoneNumberFormat;

enum PhoneNumberFormatEnum: int
{
    /**
     * Formats the NL phoneNumber "101234567" as "+31101234567"
     */
    case E164 = PhoneNumberFormat::E164->value;

    /**
     * Formats the NL phoneNumber "101234567" as "+31 10 123 4567"
     */
    case INTERNATIONAL = PhoneNumberFormat::INTERNATIONAL->value;

    /**
     * Formats the NL phoneNumber "101234567" as "010 123 4567"
     */
    case NATIONAL = PhoneNumberFormat::NATIONAL->value;

    /**
     * Formats the NL phoneNumber "101234567" as "tel:+31-10-123-4567"
     */
    case RFC3966 = PhoneNumberFormat::RFC3966->value;

    /**
     * Formats the NL phoneNumber "101234567" as "0031101234567"
     */
    case INTERNATIONAL_DIAL = 4;
}
