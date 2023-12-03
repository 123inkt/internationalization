<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Internationalization\Number\NumberFormatterSplitterResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberFormatterSplitterResult::class)]
class NumberFormatterSplitterResultTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(NumberFormatterSplitterResult::class);
    }

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
