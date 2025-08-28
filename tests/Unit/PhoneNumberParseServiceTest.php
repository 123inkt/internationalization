<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\PhoneNumberParseService;
use Generator;
use InvalidArgumentException;
use libphonenumber\PhoneNumberType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhoneNumberParseService::class)]
class PhoneNumberParseServiceTest extends TestCase
{
    public function testWithNotParsableNumber(): void
    {
        $parseService = new PhoneNumberParseService("NL");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to parse phoneNumber: abcdefg");
        $parseService->parse('abcdefg');
    }

    public function testWithInvalidNumber(): void
    {
        $parseService = new PhoneNumberParseService("NL");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Number is invalid: 12");

        $parseService->parse("12");
    }

    #[DataProvider('parseProvider')]
    public function testParse(
        string $countryCode,
        string $phoneNumber,
        string $internationalDailCode,
        string $countryDialCode,
        string $nationalNumber,
        PhoneNumberType $numberType,
        string $countryCodeFromObj,
        ?string $extension = null,
        ?string $overrideCountryCode = null
    ): void {
        $parseService = new PhoneNumberParseService($countryCode);

        $overrideCountryCode ??= $countryCode;
        $result              = $parseService->parse($phoneNumber, $overrideCountryCode);

        static::assertSame($internationalDailCode, $result->getInternationalDialCode());
        static::assertSame($countryDialCode, $result->getCountryDialCode());
        static::assertSame($nationalNumber, $result->getNationalNumber());
        static::assertSame($phoneNumber, $result->getRawInput());
        static::assertSame($countryCodeFromObj, $result->getCountryCode());
        static::assertSame($numberType, $result->getNumberType());
        static::assertSame($extension, $result->getExtension());
        static::assertSame($phoneNumber, $result->getPhoneNumber()->getRawInput());
    }

    public static function parseProvider(): Generator
    {
        yield ['BE', '09 34 44 44 32', '00', '33', '934444432', PhoneNumberType::VOIP, 'FR', null, 'FR'];
        yield ['XX', '+46522180870', '', '46', '522180870', PhoneNumberType::FIXED_LINE, 'SE'];
        yield ['SE', '+46522180870', '00', '46', '522180870', PhoneNumberType::FIXED_LINE, 'SE'];
        yield ['SE', '090-230 64 87', '00', '46', '902306487', PhoneNumberType::FIXED_LINE, 'SE'];

        yield ['US', '202-555-0107', '011', '1', '2025550107', PhoneNumberType::FIXED_LINE_OR_MOBILE, 'US'];
        yield ['US', '+1-202-555-0142', '011', '1', '2025550142', PhoneNumberType::FIXED_LINE_OR_MOBILE, 'US'];

        yield ['NL', '612345678', '00', '31', '612345678', PhoneNumberType::MOBILE, 'NL'];
        yield ['NL', '+31612345678', '00', '31', '612345678', PhoneNumberType::MOBILE, 'NL'];
        yield ['NL', '0294-787123', '00', '31', '294787123', PhoneNumberType::FIXED_LINE, 'NL'];

        yield ['FR', '01 50 12 08 32', '00', '33', '150120832', PhoneNumberType::FIXED_LINE, 'FR'];
    }
}
