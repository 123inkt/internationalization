<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Currency;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Number\NumberFormatTrimDecimalsEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CurrencyFormatOptions::class)]
class CurrencyFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        $config->setAssertParentMethods(false);
        static::assertAccessorPairs(CurrencyFormatOptions::class, $config);
    }

    public function testToString(): void
    {
        $options = new CurrencyFormatOptions();
        $options->setSymbol(true);
        $options->setCurrencyCode("EUR");
        $options->setDecimals(2);
        $options->setGrouping(false);
        $options->setLocale("nl_NL");
        $options->setTrimDecimals(NumberFormatTrimDecimalsEnum::ANY);

        $expected = 'currency:a:2:{s:8:"currency";s:3:"EUR";s:6:"symbol";b:1;}number:a:5:{s:6:"locale";s:5:"nl_NL";s:8:"grouping";b:0;s:8:' .
            '"decimals";i:2;s:4:"trim";E:63:"DR\Internationalization\Number\NumberFormatTrimDecimalsEnum:ANY";s:8:"rounding";N;}';
        $actual   = (string)$options;
        static::assertSame($expected, $actual);
    }
}
