<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\Number\NumberFormatOptions;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatOptions
 */
class NumberFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::setTrimDecimals
     * @covers ::setLocale
     * @covers ::setGrouping
     * @covers ::setDecimals
     * @covers ::getTrimDecimals
     * @covers ::getLocale
     * @covers ::isGrouping
     * @covers ::getDecimals
     * @covers ::setRounding
     * @covers ::getRounding
     */
    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        static::assertAccessorPairs(NumberFormatOptions::class, $config);
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $options = new NumberFormatOptions();
        $options->setDecimals(2);
        $options->setGrouping(false);
        $options->setLocale("nl_NL");
        $options->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ANY);

        $expected = 'number:a:5:{s:6:"locale";s:5:"nl_NL";s:8:"grouping";b:0;s:8:"decimals";i:2;s:4:"trim";i:3;s:8:"rounding";N;}';
        $actual   = (string)$options;
        static::assertSame($expected, $actual);
    }
}
