<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\PhoneNumber\PhoneNumberTypeEnum;
use DR\Internationalization\PhoneNumberParseService;
use Generator;
use InvalidArgumentException;
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
        PhoneNumberTypeEnum $numberType,
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
        yield ['BE', '09 34 44 44 32', '00', '33', '934444432', PhoneNumberTypeEnum::VOIP, 'FR', null, 'FR'];
        yield ['XX', '+46522180870', '', '46', '522180870', PhoneNumberTypeEnum::FIXED_LINE, 'SE'];
        yield ['SE', '+46522180870', '00', '46', '522180870', PhoneNumberTypeEnum::FIXED_LINE, 'SE'];
        yield ['SE', '090-230 64 87', '00', '46', '902306487', PhoneNumberTypeEnum::FIXED_LINE, 'SE'];

        yield ['US', '202-555-0107', '011', '1', '2025550107', PhoneNumberTypeEnum::FIXED_LINE_OR_MOBILE, 'US'];
        yield ['US', '+1-202-555-0142', '011', '1', '2025550142', PhoneNumberTypeEnum::FIXED_LINE_OR_MOBILE, 'US'];

        yield ['NL', '612345678', '00', '31', '612345678', PhoneNumberTypeEnum::MOBILE, 'NL'];
        yield ['NL', '+31612345678', '00', '31', '612345678', PhoneNumberTypeEnum::MOBILE, 'NL'];
        yield ['NL', '0294-787123', '00', '31', '294787123', PhoneNumberTypeEnum::FIXED_LINE, 'NL'];

        yield ['SE', '+46 -75 123 45 67', '00', '46', '751234567', PhoneNumberTypeEnum::PERSONAL_NUMBER, 'SE'];
        yield ['SE', '+46 -74 012 34 56', '00', '46', '740123456', PhoneNumberTypeEnum::PAGER, 'SE'];
        yield ['SE', '+46 -10 234 56 78', '00', '46', '102345678', PhoneNumberTypeEnum::UAN, 'SE'];
        yield ['SE', '+46 -25 412 34 56 789', '00', '46', '254123456789', PhoneNumberTypeEnum::VOICEMAIL, 'SE'];
    }
}
