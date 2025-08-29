<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatOptionsHelper;
use DR\Internationalization\Number\NumberFormatTrimDecimalsEnum;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberFormatOptionsHelper::class)]
class NumberFormatOptionsHelperTest extends TestCase
{
    public function testCurrencyShouldIgnoreIfHideEmptyDecimalsIsOffInDefaultOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    public function testCurrencyShouldIgnoreIfHideEmptyDecimalsIsOffInOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $options        = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, $options);
        static::assertSame($result, $options);
        static::assertNull($result->getDecimals());
    }

    public function testCurrencyShouldIgnoreIfValueHasDecimals(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());

        $result = $helper->applyCurrencyOptions(new Money(12345, new Currency('EUR')), null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    public function testCurrencyShouldHideDecimalsWithoutOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.0, null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());

        $result = $helper->applyCurrencyOptions(new Money(12300, new Currency('EUR')), null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());
    }

    public function testCurrencyShouldHideDecimalsWithOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $options        = (new CurrencyFormatOptions())->setGrouping(true)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.0, $options);
        static::assertNotSame($result, $options);
        static::assertTrue($result->isGrouping());
        static::assertSame(0, $result->getDecimals());

        $result = $helper->applyCurrencyOptions(new Money(12300, new Currency('EUR')), $options);
        static::assertNotSame($result, $options);
        static::assertTrue($result->isGrouping());
        static::assertSame(0, $result->getDecimals());
    }

    public function testMoneyWhichIsDividableByCurrencySubUnitTimesTenShouldNotBeRounded(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $options        = (new CurrencyFormatOptions())->setDecimals(2)
            ->setGrouping(true)
            ->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);

        $helper = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());
        // 0.20 euro should not be rounded to 0 decimals.
        $result = $helper->applyCurrencyOptions(new Money(20, new Currency('EUR')), $options);

        static::assertSame($result, $options);
        static::assertTrue($result->isGrouping());
        static::assertSame(2, $result->getDecimals());
    }

    public function testMoneyWithACurrencyThatHasZeroSubunitsShouldHaveZeroDecimals(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $options        = (new CurrencyFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);

        $helper = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());
        // XXX ISO currency has 0 subunits
        $result = $helper->applyCurrencyOptions(new Money(20, new Currency('XXX')), $options);

        static::assertNotSame($result, $options);
        static::assertSame(0, $result->getDecimals());
    }

    public function testNumberShouldIgnoreIfNoDecimalsAreSet(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    public function testNumberShouldIgnoreIfNoDecimalsIsAlreadyZero(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $options        = (new NumberFormatOptions())->setDecimals(0)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, $options);
        static::assertSame($result, $options);
        static::assertSame(0, $result->getDecimals());
    }

    public function testNumberShouldIgnoreIfHideEmptyDecimalsIsOffInDefaultOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertSame(4, $result->getDecimals());
    }

    public function testNumberShouldIgnoreIfHideEmptyDecimalsIsOffInOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, $options);
        static::assertSame($result, $options);
        static::assertNull($result->getDecimals());
    }

    public function testNumberShouldIgnoreIfValueHasDecimals(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertSame(4, $result->getDecimals());
    }

    public function testNumberShouldHideDecimalsWithoutOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());
    }

    public function testNumberShouldHideDecimalsWithOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatTrimDecimalsEnum::NONE);
        $options        = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatTrimDecimalsEnum::ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, $options);
        static::assertNotSame($result, $options);
        static::assertSame(0, $result->getDecimals());
    }
}
