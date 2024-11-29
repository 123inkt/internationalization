<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DateTimeImmutable;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\RelativeDateFallbackResult;
use DR\Internationalization\Date\RelativeDateFallbackService;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use DR\Internationalization\Date\RelativeDateFormatterFactory;
use Generator;
use IntlDateFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(RelativeDateFallbackService::class)]
class RelativeDateFallbackServiceTest extends TestCase
{
    private RelativeDateFormatterFactory&MockObject $dateFormatterFactory;
    private RelativeDateFallbackService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dateFormatterFactory = $this->createMock(RelativeDateFormatterFactory::class);
        $this->service = new RelativeDateFallbackService($this->dateFormatterFactory);
    }

    #[DataProvider('dataProviderFallback')]
    public function testFallback($datetime, $relativeOptions, $relativeFullDate, $fullDate, $amountFactoryCalls, $expectedResult): void
    {
        $options = new DateFormatOptions('en_GB', 'Europe/Amsterdam');
        $relativeFormatter = $this->createMock(IntlDateFormatter::class);
        $fullDateFormatter = $this->createMock(IntlDateFormatter::class);
        $relativeFormatter->expects(self::exactly($amountFactoryCalls))->method('format')->willReturn($relativeFullDate);
        $fullDateFormatter->expects(self::exactly($amountFactoryCalls))->method('format')->willReturn($fullDate);

        $this->dateFormatterFactory
            ->expects(self::exactly($amountFactoryCalls))
            ->method('createRelativeFull')
            ->with($options)
            ->willReturn($relativeFormatter);

        $this->dateFormatterFactory
            ->expects(self::exactly($amountFactoryCalls))
            ->method('createFull')
            ->with($options)
            ->willReturn($fullDateFormatter);

        $result = $this->service->getFallbackResult($options, $datetime, $relativeOptions);

        static::assertSame($expectedResult->isFallback(), $result->isFallback());
        static::assertSame($expectedResult->getDate(), $result->getDate());
    }

    public function testThrowException(): void
    {
        $options = new DateFormatOptions('en_GB', 'Europe/Amsterdam');
        $relativeFormatter = $this->createMock(IntlDateFormatter::class);
        $fullDateFormatter = $this->createMock(IntlDateFormatter::class);
        $relativeFormatter->expects(self::once())->method('format')->willReturn(false);
        $fullDateFormatter->expects(self::once())->method('format')->willReturn(false);

        $relativeFormatter->expects(self::once())->method('getErrorCode')->willReturn(101);
        $relativeFormatter->expects(self::once())->method('getErrorMessage')->willReturn('Error!');

        $this->dateFormatterFactory
            ->expects(self::exactly(1))
            ->method('createRelativeFull')
            ->with($options)
            ->willReturn($relativeFormatter);

        $this->dateFormatterFactory
            ->expects(self::exactly(1))
            ->method('createFull')
            ->with($options)
            ->willReturn($fullDateFormatter);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while trying to parse the relative date. Error code: 101, Error!');
        $this->service->getFallbackResult($options, new DateTimeImmutable('+2 days'), new RelativeDateFormatOptions(10));
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
            0,
            new RelativeDateFallbackResult(true)
        ];
        yield 'Fallback relative options zero' => [
            new DateTimeImmutable('+1 days'),
            new RelativeDateFormatOptions(0),
            'Tomorrow',
            '2024-01-01',
            0,
            new RelativeDateFallbackResult(true)
        ];
        yield 'Fallback RelativeOptions difference too big' => [
            new DateTimeImmutable('+3 days'),
            new RelativeDateFormatOptions(2),
            '2024-01-01',
            '2024-01-04',
            0,
            new RelativeDateFallbackResult(true)
        ];
        yield 'Fallback date same' => [
            new DateTimeImmutable('+3 days'),
            new RelativeDateFormatOptions(5),
            '2024-01-01',
            '2024-01-01',
            1,
            new RelativeDateFallbackResult(true)
        ];
        yield 'Should not fallback' => [
            new DateTimeImmutable('+1 days'),
            new RelativeDateFormatOptions(5),
            'Tomorrow',
            '2024-01-04',
            1,
            new RelativeDateFallbackResult(false, 'Tomorrow')
        ];
    }
}
