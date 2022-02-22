<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatterFactoryHelper;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatterFactoryHelper
 */
class NumberFormatterFactoryHelperTest extends TestCase
{
    /**
     * @covers ::applyNumberFormatOptions
     */
    public function testApplyNumberFormatOptionsWithDefault(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = new NumberFormatOptions();
        $options        = new NumberFormatOptions();

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(NumberFormatter::ROUND_HALFUP, $formatter->getAttribute(NumberFormatter::ROUNDING_MODE));
        static::assertSame(1, $formatter->getAttribute(NumberFormatter::GROUPING_USED));
        static::assertSame(3, $formatter->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
        static::assertSame(0, $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS));
    }

    /**
     * @covers ::applyNumberFormatOptions
     */
    public function testApplyNumberFormatOptionsWithRoundingAndGrouping(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = (new NumberFormatOptions())->setRounding(NumberFormatter::ROUND_CEILING);
        $options        = (new NumberFormatOptions())->setGrouping(false);

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(NumberFormatter::ROUND_CEILING, $formatter->getAttribute(NumberFormatter::ROUNDING_MODE));
        static::assertSame(0, $formatter->getAttribute(NumberFormatter::GROUPING_USED));
        static::assertSame(3, $formatter->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
        static::assertSame(0, $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS));
    }

    /**
     * @covers ::applyNumberFormatOptions
     */
    public function testApplyNumberFormatOptionsWithSpecificDecimals(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = new NumberFormatOptions();
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE)->setDecimals(5);

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(5, $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS));
    }

    /**
     * @covers ::applyNumberFormatOptions
     */
    public function testApplyNumberFormatOptionsWithTrimmedDecimals(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = new NumberFormatOptions();
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ANY)->setDecimals(5);

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(5, $formatter->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
    }
}
