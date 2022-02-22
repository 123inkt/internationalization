<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Currency;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\Currency\CurrencyFormatOptions;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Currency\CurrencyFormatOptions
 */
class CurrencyFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::setSymbol
     * @covers ::hasSymbol
     * @covers ::setCurrencyCode
     * @covers ::getCurrencyCode
     */
    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        $config->setAssertParentMethods(false);
        static::assertAccessorPairs(CurrencyFormatOptions::class, $config);
    }

    /**
     * @covers ::__toString
     */
    public function testToString(): void
    {
        $options = new CurrencyFormatOptions();
        $options->setSymbol(true);
        $options->setCurrencyCode("EUR");
        $options->setDecimals(2);
        $options->setGrouping(false);
        $options->setLocale("nl_NL");
        $options->setTrimDecimals(CurrencyFormatOptions::TRIM_DECIMAL_ANY);

        $expected = 'currency:a:2:{s:8:"currency";s:3:"EUR";s:6:"symbol";b:1;}number:a:5:{s:6:"locale";s:5:"nl_NL";s:8:"grouping";b:0;s:8:' .
            '"decimals";i:2;s:4:"trim";i:3;s:8:"rounding";N;}';
        $actual   = (string)$options;
        static::assertSame($expected, $actual);
    }
}
