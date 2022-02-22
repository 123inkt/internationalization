<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberParser
 */
class NumberParserTest extends TestCase
{
    /**
     * @covers ::parseFloat
     * @covers ::determineSeparators
     */
    public function testParseFloatSuccess(): void
    {
        // no separator
        static::assertSame(1234.0, NumberParser::parseFloat('1234'));

        // single separator
        static::assertSame(1.234, NumberParser::parseFloat('1.234'));
        static::assertSame(1.234, NumberParser::parseFloat('1,234'));
        static::assertSame(1.234, NumberParser::parseFloat('01,234'));
        static::assertSame(1.0, NumberParser::parseFloat('1,'));
        static::assertSame(-1.234, NumberParser::parseFloat('-1,234'));

        // decimal + thousand separator
        static::assertSame(1000.34, NumberParser::parseFloat('1,000.34'));
        static::assertSame(1000.34, NumberParser::parseFloat('1.000,34'));
        static::assertSame(-1000.34, NumberParser::parseFloat('-1.000,34'));

        // decimal + multi thousand separator
        static::assertSame(1000000.34, NumberParser::parseFloat('1,000,000.34'));
        static::assertSame(1000000.34, NumberParser::parseFloat('1.000.000,34'));
        static::assertSame(10000000.34, NumberParser::parseFloat('10,000,000.34'));
        static::assertSame(10000000.34, NumberParser::parseFloat('10.000.000,34'));
        static::assertSame(-10000000.34, NumberParser::parseFloat('-10.000.000,34'));

        // multi thousand separator
        static::assertSame(1000000.0, NumberParser::parseFloat('1,000,000'));
        static::assertSame(1000000.0, NumberParser::parseFloat('1.000.000'));
        static::assertSame(10000000.0, NumberParser::parseFloat('10,000,000'));
        static::assertSame(10000000.0, NumberParser::parseFloat('10.000.000'));
        static::assertSame(-10000000.0, NumberParser::parseFloat('-10.000.000'));

        // whitespace
        static::assertSame(1000000.0, NumberParser::parseFloat(' 1,000,000 '));
        static::assertSame(1000000.0, NumberParser::parseFloat('1 000 000'));
        static::assertSame(1000.0, NumberParser::parseFloat("1\xC2\xA0000"));
        static::assertSame(1000.0, NumberParser::parseFloat("1\xE2\x80\xAF000"));
        static::assertSame(1000000.0, NumberParser::parseFloat(' 1 0 0 0 0 0 0 '));
        static::assertSame(1000000.23, NumberParser::parseFloat(' 1 0 0 0 0 0 0 . 2 3 '));

        // rounding
        static::assertSame(123.0, NumberParser::parseFloat('123', 2));
        static::assertSame(1.23, NumberParser::parseFloat('1,234', 2));
        static::assertSame(1000.35, NumberParser::parseFloat('1,000.345', 2));
        static::assertSame(1000000.35, NumberParser::parseFloat('1,000,000.345', 2));
        static::assertSame(1000000.0, NumberParser::parseFloat('1,000,000', 2));
    }

    /**
     * @covers ::parseFloat
     * @covers ::determineSeparators
     */
    public function testParseFloatFailure(): void
    {
        // invalid value
        static::assertFalse(NumberParser::parseFloat(''));
        static::assertFalse(NumberParser::parseFloat(' '));
        static::assertFalse(NumberParser::parseFloat('+1234'));
        static::assertFalse(NumberParser::parseFloat('€ 1234'));

        // multi decimal separator
        static::assertFalse(NumberParser::parseFloat('1,000,000.000.00'));

        // incorrect thousand separator
        static::assertFalse(NumberParser::parseFloat('1,000,00'));
        static::assertFalse(NumberParser::parseFloat('1.000.00'));
        static::assertFalse(NumberParser::parseFloat('1.00,000'));
        static::assertFalse(NumberParser::parseFloat('1000.000.000'));
        static::assertFalse(NumberParser::parseFloat('1,000.000,34'));
    }
}
