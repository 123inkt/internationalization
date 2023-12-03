<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions;
use DR\Internationalization\PhoneNumberFormatService;
use DR\Internationalization\PhoneNumberParseService;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhoneNumberFormatService::class)]
class PhoneNumberFormatServiceTest extends TestCase
{
    public function testFormatMissingOption(): void
    {
        $formatter = new PhoneNumberFormatService((new PhoneNumberFormatOptions())->setDefaultCountryCode("NL"));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('PhoneNumberOptions: unable to format phoneNumber without a given format');
        $formatter->format('0612345678');
    }

    public function testFormatInvalidInput(): void
    {
        $options   = (new PhoneNumberFormatOptions())->setDefaultCountryCode("__")->setFormat(PhoneNumberFormatOptions::FORMAT_NATIONAL);
        $formatter = new PhoneNumberFormatService($options);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unable to parse phoneNumber: xxx");
        $formatter->format('xxx');
    }

    #[DataProvider('optionFormatProvider')]
    public function testFormat(int $format, string $phoneNumber, string $expectedValue): void
    {
        $formatter = new PhoneNumberFormatService((new PhoneNumberFormatOptions())->setDefaultCountryCode("NL")->setFormat($format));
        static::assertSame($expectedValue, $formatter->format($phoneNumber));
    }

    #[DataProvider('internationalDialProvider')]
    public function testFormatInternationDial(string $countryCode, string $phoneNumber, string $expectedValue): void
    {
        $options = (new PhoneNumberFormatOptions())
            ->setDefaultCountryCode($countryCode)
            ->setFormat(PhoneNumberFormatOptions::FORMAT_INTERNATIONAL_DIAL);

        $formatter = new PhoneNumberFormatService($options);
        static::assertSame($expectedValue, $formatter->format($phoneNumber));
    }

    public function testFormatDefaultFormat(): void
    {
        $defaultOptions = (new PhoneNumberFormatOptions())->setDefaultCountryCode('NL')->setFormat(PhoneNumberFormatOptions::FORMAT_NATIONAL);
        $formatter      = new PhoneNumberFormatService($defaultOptions);

        static::assertSame('010 123 4567', $formatter->format("101234567"));
        static::assertSame('06 12345678', $formatter->format("0612345678"));
    }

    public function testFormatOverwrittenCountryCode(): void
    {
        $defaultOptions = (new PhoneNumberFormatOptions())->setDefaultCountryCode('NL')->setFormat(PhoneNumberFormatOptions::FORMAT_NATIONAL);
        $formatOptions  = (new PhoneNumberFormatOptions())->setDefaultCountryCode('GB');
        $formatter      = new PhoneNumberFormatService($defaultOptions);

        static::assertSame('0121 234 5678', $formatter->format("1212345678", $formatOptions));
        static::assertSame('07400 123456', $formatter->format("7400123456", $formatOptions));
    }

    public function testFormatFromParsedPhoneNumberObject(): void
    {
        $parsedPhoneNumber = (new PhoneNumberParseService("NL"))->parse("0612345678");

        $defaultOptions = (new PhoneNumberFormatOptions())->setDefaultCountryCode('NL')->setFormat(PhoneNumberFormatOptions::FORMAT_NATIONAL);
        $formatter      = new PhoneNumberFormatService($defaultOptions);

        static::assertSame('06 12345678', $formatter->format($parsedPhoneNumber));
    }

    public static function optionFormatProvider(): Generator
    {
        yield [PhoneNumberFormatOptions::FORMAT_E164, "101234567", "+31101234567"];
        yield [PhoneNumberFormatOptions::FORMAT_E164, "0612345678", "+31612345678"];

        yield [PhoneNumberFormatOptions::FORMAT_INTERNATIONAL, "101234567", "+31 10 123 4567"];
        yield [PhoneNumberFormatOptions::FORMAT_INTERNATIONAL, "0612345678", "+31 6 12345678"];

        yield [PhoneNumberFormatOptions::FORMAT_NATIONAL, "101234567", "010 123 4567"];
        yield [PhoneNumberFormatOptions::FORMAT_NATIONAL, "0612345678", "06 12345678"];

        yield [PhoneNumberFormatOptions::FORMAT_RFC3966, "101234567", "tel:+31-10-123-4567"];
        yield [PhoneNumberFormatOptions::FORMAT_RFC3966, "0612345678", "tel:+31-6-12345678"];
    }

    public static function internationalDialProvider(): Generator
    {
        yield ['NL', '612345678', '0031612345678'];
        yield ['NL', '0612345678', '0031612345678'];
        yield ['NL', '+31612345678', '0031612345678'];
        yield ['NL', '0031612345678', '0031612345678'];

        yield ['BE', '412345678', '0032412345678'];
        yield ['BE', '+32412345678', '0032412345678'];
        yield ['BE', '0032412345678', '0032412345678'];

        yield ['US', '+31612345678', '01131612345678'];
        yield ['US', '+32412345678', '01132412345678'];
        yield ['US', '5062345678', '01115062345678'];
        yield ['US', '+15062345678', '01115062345678'];

        // BR internationalPrefix is'00(?:1[245]|2[1-35]|31|4[13]|[56]5|99)', and has no preferredInternationalPrefix
        yield ['BR', '+1 201-555-0123', '+12015550123'];
    }
}
