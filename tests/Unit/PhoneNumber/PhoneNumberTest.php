<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\PhoneNumber;

use DR\Internationalization\PhoneNumber\PhoneNumber;
use DR\Internationalization\PhoneNumber\PhoneNumberTypeEnum;
use libphonenumber\PhoneNumber as LibPhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\PhoneNumber\PhoneNumber
 */
class PhoneNumberTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getInternationalDialCode
     * @covers ::getCountryDialCode
     * @covers ::getNationalNumber
     * @covers ::getExtension
     * @covers ::getRawInput
     * @covers ::getCountryCode
     * @covers ::getNumberType
     * @covers ::getPhoneNumber
     */
    public function testGetters()
    {
        $phoneNumberObj = new PhoneNumber('00', '31', '612345678', '+31612345678', 'NL', PhoneNumberTypeEnum::MOBILE, new LibPhoneNumber(), null);

        static::assertSame('00', $phoneNumberObj->getInternationalDialCode());
        static::assertSame('31', $phoneNumberObj->getCountryDialCode());
        static::assertSame('612345678', $phoneNumberObj->getNationalNumber());
        static::assertSame('+31612345678', $phoneNumberObj->getRawInput());
        static::assertSame('NL', $phoneNumberObj->getCountryCode());
        static::assertSame(PhoneNumberTypeEnum::MOBILE, $phoneNumberObj->getNumberType());
        static::assertNotNull($phoneNumberObj->getPhoneNumber());
        static::assertNull($phoneNumberObj->getExtension());
    }
}
