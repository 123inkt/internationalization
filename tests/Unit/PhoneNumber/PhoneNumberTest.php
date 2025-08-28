<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\PhoneNumber;

use DR\Internationalization\PhoneNumber\PhoneNumber;
use libphonenumber\PhoneNumber as LibPhoneNumber;
use libphonenumber\PhoneNumberType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhoneNumber::class)]
class PhoneNumberTest extends TestCase
{
    public function testGetters()
    {
        $phoneNumberObj = new PhoneNumber('00', '31', '612345678', '+31612345678', 'NL', PhoneNumberType::MOBILE, new LibPhoneNumber(), null);

        static::assertSame('00', $phoneNumberObj->getInternationalDialCode());
        static::assertSame('31', $phoneNumberObj->getCountryDialCode());
        static::assertSame('612345678', $phoneNumberObj->getNationalNumber());
        static::assertSame('+31612345678', $phoneNumberObj->getRawInput());
        static::assertSame('NL', $phoneNumberObj->getCountryCode());
        static::assertSame(PhoneNumberType::MOBILE, $phoneNumberObj->getNumberType());
        static::assertNotNull($phoneNumberObj->getPhoneNumber());
        static::assertNull($phoneNumberObj->getExtension());
    }
}
