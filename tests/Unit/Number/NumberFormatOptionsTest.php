<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatTrimDecimalsEnum;
use NumberFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberFormatOptions::class)]
class NumberFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        static::assertAccessorPairs(NumberFormatOptions::class, $config);
    }

    public function testTrimDecimals(): void
    {
        $options = new NumberFormatOptions();
        $options->setTrimDecimals(NumberFormatTrimDecimalsEnum::ANY);
        static::assertSame(NumberFormatTrimDecimalsEnum::ANY, $options->getTrimDecimals());
    }

    public function testRounding(): void
    {
        $options = new NumberFormatOptions();
        $options->setRounding(NumberFormatter::ROUND_DOWN);
        static::assertSame(NumberFormatter::ROUND_DOWN, $options->getRounding());
    }

    public function testToString(): void
    {
        $options = new NumberFormatOptions();
        $options->setDecimals(2);
        $options->setGrouping(false);
        $options->setLocale("nl_NL");
        $options->setTrimDecimals(NumberFormatTrimDecimalsEnum::ANY);

        $expected = 'number:a:5:{s:6:"locale";s:5:"nl_NL";s:8:"grouping";b:0;s:8:"decimals";i:2;s:4:"trim";' .
            'E:63:"DR\Internationalization\Number\NumberFormatTrimDecimalsEnum:ANY";s:8:"rounding";N;}';
        $actual   = (string)$options;
        static::assertSame($expected, $actual);
    }
}
