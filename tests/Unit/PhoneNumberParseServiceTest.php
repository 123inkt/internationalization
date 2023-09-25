<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\PhoneNumber\PhoneNumber;
use DR\Internationalization\PhoneNumber\PhoneNumberTypeEnum;
use DR\Internationalization\PhoneNumberParseService;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\PhoneNumberParseService
 * @covers ::__construct
 */
class PhoneNumberParseServiceTest extends TestCase
{
    /**
     * @covers ::parse
     */
    public function testWithNotParsableNumber(): void
    {
        $parseService = new PhoneNumberParseService("NL");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to parse phoneNumber: abcdefg");
        $parseService->parse('abcdefg');
    }

    /**
     * @covers ::parse
     */
    public function testWithInvalidNumber(): void
    {
        $parseService = new PhoneNumberParseService("NL");
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Number is invalid: 12");

        $parseService->parse("12");
    }

    /**
     * @dataProvider parseProvider
     * @covers ::parse
     * @covers ::getNumberType
     */
    public function testParse(
        string $countryCode,
        string $phoneNumber,
        string $internationalDailCode,
        string $countryDialCode,
        string $nationalNumber,
        PhoneNumberTypeEnum $numberType,
        ?string $extension = null
    ): void {
        $parseService = new PhoneNumberParseService($countryCode);
        $result = $parseService->parse($phoneNumber);

        static::assertSame($internationalDailCode, $result->getInternationalDialCode());
        static::assertSame($countryDialCode, $result->getCountryDialCode());
        static::assertSame($nationalNumber, $result->getNationalNumber());
        static::assertSame($phoneNumber, $result->getRawInput());
        static::assertSame($countryCode, $result->getCountryCode());
        static::assertSame($numberType, $result->getNumberType());
        static::assertSame($extension, $result->getExtension());
        static::assertSame($phoneNumber, $result->getPhoneNumber()->getRawInput());
    }


    public function parseProvider(): Generator
    {
        yield ['SE', '+46522180870', '00', '46', '522180870', PhoneNumberTypeEnum::FIXED_LINE];
        yield ['SE', '090-230 64 87', '00', '46', '902306487', PhoneNumberTypeEnum::FIXED_LINE];

        yield ['US', '202-555-0107', '011', '1', '2025550107', PhoneNumberTypeEnum::FIXED_LINE_OR_MOBILE];
        yield ['US', '+1-202-555-0142', '011', '1', '2025550142', PhoneNumberTypeEnum::FIXED_LINE_OR_MOBILE];

        yield ['NL', '612345678', '00', '31', '612345678', PhoneNumberTypeEnum::MOBILE];
        yield ['NL', '+31612345678', '00', '31', '612345678', PhoneNumberTypeEnum::MOBILE];
        yield ['NL', '0294-787123', '00', '31', '294787123', PhoneNumberTypeEnum::FIXED_LINE];

        yield ['FR', '01 50 12 08 32', '00', '33', '150120832', PhoneNumberTypeEnum::FIXED_LINE];
    }
}
