<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\DateFormatService;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\DateFormatService
 * @covers ::__construct
 */
class DateFormatServiceTest extends TestCase
{
    /**
     * @covers ::format
     * @covers ::getDateFormatter
     * @dataProvider dataProviderDateFormats
     */
    public function testFormat($locale, $timeZone, $value, $format, $expectedValue): void
    {
        $formatService = new DateFormatService($locale, $timeZone);
        static::assertSame($expectedValue, $formatService->format($value, $format));
    }

    /**
     * @return Generator<string, array<string>>
     */
    public function dataProviderDateFormats(): Generator
    {
        yield 'nl_NL, Y-M-d' => ['nl_NL', 'Europe/Amsterdam', 2222222222, 'Y-M-d', '2040-6-2'];
        yield 'nl_NL, string input' => ['nl_NL', 'Europe/Amsterdam', '2022-08-16 + 1 day', 'Y-M-d', '2022-8-17'];
        yield 'nl_NL, DateTime input' => ['nl_NL', 'Europe/Amsterdam', new \DateTime('2022-08-16 + 1 day'), 'Y-M-d', '2022-8-17'];
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

    /**
     * @covers ::format
     * @covers ::getDateFormatter
     */
    public function testFormatDuplicateFormat(): void
    {
        $formatService = new DateFormatService('nl_NL', 'Europe/Amsterdam');
        static::assertSame('zaterdag 02 juni 2040 - 05:57:02', $formatService->format(2222222222, 'eeee dd LLLL Y - HH:mm:ss'));
        static::assertSame('zaterdag 02 juni 2040 - 05:57:02', $formatService->format(2222222222, 'eeee dd LLLL Y - HH:mm:ss'));
    }
}
