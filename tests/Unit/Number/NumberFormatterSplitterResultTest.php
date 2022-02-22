<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Internationalization\Number\NumberFormatterSplitterResult;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatterSplitterResult
 * @covers ::__construct
 */
class NumberFormatterSplitterResultTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::getValue
     * @covers ::getSymbol
     * @covers ::getSymbolPosition
     * @covers ::getGroupingSeparator
     * @covers ::getDecimalSeparator
     * @covers ::getPrefix
     * @covers ::getSuffix
     * @covers ::getInteger
     * @covers ::getDecimals
     */
    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(NumberFormatterSplitterResult::class);
    }

    /**
     * @covers ::getNumberValue
     */
    public function testGetNumberValueWithDecimals(): void
    {
        $result = new NumberFormatterSplitterResult(
            '€ 1.234,56',
            '€ ',
            ' ',
            '1.234',
            '.',
            '56',
            ',',
            '€',
            NumberFormatterSplitterResult::POSITION_BEFORE
        );
        static::assertSame('1.234,56', $result->getNumberValue());
    }

    /**
     * @covers ::getNumberValue
     */
    public function testGetNumberValueWithoutDecimals(): void
    {
        $result = new NumberFormatterSplitterResult(
            '€ 1.234',
            '€ ',
            ' ',
            '1.234',
            '.',
            '',
            ',',
            '€',
            NumberFormatterSplitterResult::POSITION_BEFORE
        );
        static::assertSame('1.234', $result->getNumberValue());
    }
}
