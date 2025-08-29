<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatterFactoryHelper;
use DR\Internationalization\Number\NumberFormatTrimDecimalsEnum;
use NumberFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberFormatterFactoryHelper::class)]
class NumberFormatterFactoryHelperTest extends TestCase
{
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

    public function testApplyNumberFormatOptionsWithSpecificDecimals(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = new NumberFormatOptions();
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE)->setDecimals(5);

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(5, $formatter->getAttribute(NumberFormatter::FRACTION_DIGITS));
    }

    public function testApplyNumberFormatOptionsWithTrimmedDecimals(): void
    {
        $formatter      = new NumberFormatter('nl_NL', NumberFormatter::DECIMAL);
        $defaultOptions = new NumberFormatOptions();
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::ANY)->setDecimals(5);

        NumberFormatterFactoryHelper::applyNumberFormatOptions($defaultOptions, $options, $formatter);

        static::assertSame(5, $formatter->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
    }
}
