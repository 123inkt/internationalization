<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DateTimeImmutable;
use DR\Internationalization\Date\RelativeDateFallbackService;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RelativeDateFallbackService::class)]
class RelativeDateFallbackServiceTest extends TestCase
{
    private RelativeDateFallbackService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RelativeDateFallbackService();
    }

    #[DataProvider('dataProviderFallback')]
    public function testFallback($datetime, $relativeOptions, $defaultDate, $actualDate, $expectedResult): void
    {
        $result = $this->service->shouldFallback($datetime, $relativeOptions, $defaultDate, $actualDate);

        static::assertSame($expectedResult, $result);
    }

    /**
     * @return Generator<string, array<string|RelativeDateFormatOptions|DateTimeImmutable|bool>>
     */
    public static function dataProviderFallback(): Generator
    {
        yield 'Fallback above max days' => [
            new DateTimeImmutable('+6 days'),
            new RelativeDateFormatOptions(10),
            '2024-01-01',
            '2024-01-07',
            true
        ];
        yield 'Fallback relative options zero' => [
            new DateTimeImmutable('+1 days'),
            new RelativeDateFormatOptions(0),
            '2024-01-01',
            'Tomorrow',
            true
        ];
        yield 'Fallback RelativeOptions difference too big' => [
            new DateTimeImmutable('+3 days'),
            new RelativeDateFormatOptions(2),
            '2024-01-01',
            '2024-01-04',
            true
        ];
        yield 'Should not fallback' => [
            new DateTimeImmutable('+1 days'),
            new RelativeDateFormatOptions(5),
            '2024-01-04',
            'Tomorrow',
            false
        ];
    }
}
