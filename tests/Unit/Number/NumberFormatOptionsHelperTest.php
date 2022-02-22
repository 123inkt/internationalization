<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatOptionsHelper;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatOptionsHelper
 * @covers ::__construct
 */
class NumberFormatOptionsHelperTest extends TestCase
{
    /**
     * @covers ::applyCurrencyOptions
     * @covers ::currencyHasDecimals
     */
    public function testCurrencyShouldIgnoreIfHideEmptyDecimalsIsOffInDefaultOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    /**
     * @covers ::applyCurrencyOptions
     * @covers ::currencyHasDecimals
     */
    public function testCurrencyShouldIgnoreIfHideEmptyDecimalsIsOffInOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $options        = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, $options);
        static::assertSame($result, $options);
        static::assertNull($result->getDecimals());
    }

    /**
     * @covers ::applyCurrencyOptions
     * @covers ::hideDecimals
     * @covers ::currencyHasDecimals
     */
    public function testCurrencyShouldIgnoreIfValueHasDecimals(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());

        $result = $helper->applyCurrencyOptions(new Money(12345, new Currency('EUR')), null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    /**
     * @covers ::applyCurrencyOptions
     * @covers ::hideDecimals
     * @covers ::currencyHasDecimals
     */
    public function testCurrencyShouldHideDecimalsWithoutOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper($defaultOptions, new NumberFormatOptions(), new ISOCurrencies());

        $result = $helper->applyCurrencyOptions(123.0, null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());

        $result = $helper->applyCurrencyOptions(new Money(12300, new Currency('EUR')), null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());
    }

    /**
     * @covers ::applyCurrencyOptions
     * @covers ::hideDecimals
     * @covers ::currencyHasDecimals
     */
    public function testCurrencyShouldHideDecimalsWithOptions(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $options        = (new CurrencyFormatOptions())->setGrouping(true)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
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

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldIgnoreIfNoDecimalsAreSet(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, null);
        static::assertSame($result, $defaultOptions);
        static::assertNull($result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldIgnoreIfNoDecimalsIsAlreadyZero(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $options        = (new NumberFormatOptions())->setDecimals(0)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, $options);
        static::assertSame($result, $options);
        static::assertSame(0, $result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldIgnoreIfHideEmptyDecimalsIsOffInDefaultOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertSame(4, $result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldIgnoreIfHideEmptyDecimalsIsOffInOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $options        = (new NumberFormatOptions())->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, $options);
        static::assertSame($result, $options);
        static::assertNull($result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldIgnoreIfValueHasDecimals(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.45, null);
        static::assertSame($result, $defaultOptions);
        static::assertSame(4, $result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldHideDecimalsWithoutOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, null);
        static::assertNotSame($result, $defaultOptions);
        static::assertSame(0, $result->getDecimals());
    }

    /**
     * @covers ::applyNumberOptions
     * @covers ::hideDecimals
     */
    public function testNumberShouldHideDecimalsWithOptions(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setDecimals(4)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $options        = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $helper         = new NumberFormatOptionsHelper(new CurrencyFormatOptions(), $defaultOptions, new ISOCurrencies());

        $result = $helper->applyNumberOptions(123.0, $options);
        static::assertNotSame($result, $options);
        static::assertSame(0, $result->getDecimals());
    }
}
