<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DateTime;
use DateTimeImmutable;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use DR\Internationalization\DateFormatService;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatService::class)]
class DateFormatServiceTest extends TestCase
{
    #[DataProvider('dataProviderDateFormats')]
    public function testFormat($locale, $timeZone, $value, $format, $expectedValue): void
    {
        $formatService = new DateFormatService(new DateFormatOptions($locale, $timeZone));
        static::assertSame($expectedValue, $formatService->format($value, $format));
    }

    #[DataProvider('dataProviderRelativeDateFormats')]
    public function testRelativeFormat($locale, $timeZone, $value, $relativeOptions, $fallbackOptions, $expectedValue): void
    {
        $formatService = new DateFormatService(new DateFormatOptions($locale, $timeZone));
        static::assertSame($expectedValue, $formatService->formatRelative($value, 'Y-M-d', $relativeOptions, $fallbackOptions));
    }

    /**
     * @return Generator<string, array<string|RelativeDateFormatOptions|DateFormatOptions>>
     */
    public static function dataProviderRelativeDateFormats(): Generator
    {
        $fallbackOption = new DateFormatOptions('en_GB', 'UTC');

        yield 'nl_NL, no relative' => [
            'en_GB', 'UTC',
            new DateTimeImmutable(),
            new RelativeDateFormatOptions(0),
            $fallbackOption,
            (new DateTimeImmutable())->format('Y-m-d')
        ];
        yield 'nl_NL, relative today' => [
            'en_GB', 'UTC',
            new DateTimeImmutable(),
            new RelativeDateFormatOptions(2),
            $fallbackOption,
            'today'
        ];
        yield 'nl_NL, relative 1 day' => [
            'en_GB', 'UTC',
            new DateTimeImmutable('+1 day'),
            new RelativeDateFormatOptions(2),
            $fallbackOption,
            'tomorrow'
        ];
        yield 'nl_NL, relative 2 days Dutch' => [
            'nl_NL', 'Europe/Amsterdam',
            new DateTimeImmutable('+2 days'),
            new RelativeDateFormatOptions(2),
            $fallbackOption,
            'overmorgen'
        ];
        yield 'nl_NL, relative day but capped by options' => [
            'nl_NL', 'UTC',
            new DateTimeImmutable('+2 days'),
            new RelativeDateFormatOptions(1),
            $fallbackOption,
            (new DateTimeImmutable('+2 days'))->format('Y-m-d')
        ];
        yield 'nl_NL, relative 2 days Dutch int' => [
            'nl_NL', 'Europe/Amsterdam',
            (new DateTimeImmutable('+2 days'))->getTimestamp(),
            new RelativeDateFormatOptions(2),
            $fallbackOption,
            'overmorgen'
        ];
        yield 'nl_NL, relative 2 days Dutch string' => [
            'nl_NL', 'Europe/Amsterdam',
            (new DateTimeImmutable('+2 days'))->format('Y-m-d'),
            new RelativeDateFormatOptions(2),
            $fallbackOption,
            'overmorgen'
        ];
    }

    /**
     * @return Generator<string, array<string>>
     */
    public static function dataProviderDateFormats(): Generator
    {
        yield 'nl_NL, Y-M-d' => ['nl_NL', 'Europe/Amsterdam', 2222222222, 'Y-M-d', '2040-6-2'];
        yield 'nl_NL, string input' => ['nl_NL', 'Europe/Amsterdam', '2022-08-16 + 1 day', 'Y-M-d', '2022-8-17'];
        yield 'nl_NL, DateTime input' => ['nl_NL', 'Europe/Amsterdam', new DateTime('2022-08-16 + 1 day'), 'Y-M-d', '2022-8-17'];
        yield 'nl_NL, long format' => [
            'nl_NL',
            'Europe/Amsterdam',
            2222222222,
            'eeee dd LLLL Y - HH:mm:ss',
            'zaterdag 02 juni 2040 - 05:57:02'
        ];
        yield 'en_GBL, Y-M-d' => ['en_GB', 'Europe/London', 2222222222, 'Y-M-d', '2040-6-2'];
        yield 'en_GB, long format' => [
            'en_GB',
            'Europe/Amsterdam',
            2222222222,
            'eeee dd LLLL Y - HH:mm:ss',
            'Saturday 02 June 2040 - 05:57:02'
        ];
        yield 'en_GB, other timezone' => [
            'en_GB',
            'Europe/London',
            2222222222,
            'eeee dd LLLL Y - HH:mm:ss',
            'Saturday 02 June 2040 - 04:57:02'
        ];
    }

    public function testFormatDuplicateFormat(): void
    {
        $formatService = new DateFormatService('nl_NL', 'Europe/Amsterdam');
        static::assertSame('zaterdag 02 juni 2040 - 05:57:02', $formatService->format(2222222222, 'eeee dd LLLL Y - HH:mm:ss'));
        static::assertSame('zaterdag 02 juni 2040 - 05:57:02', $formatService->format(2222222222, 'eeee dd LLLL Y - HH:mm:ss'));
    }
}
