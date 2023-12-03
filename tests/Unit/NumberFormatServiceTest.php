<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatterCacheInterface;
use DR\Internationalization\Number\NumberFormatterSplitterResult as Result;
use DR\Internationalization\NumberFormatService;
use Generator;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\NumberFormatService
 * @covers ::__construct
 */
class NumberFormatServiceTest extends TestCase
{
    private const NBSP  = "\xC2\xA0";
    private const NNBSP = "\xE2\x80\xAF";

    private CurrencyFormatOptions $currencyFormatOptions;
    private NumberFormatOptions   $numberFormatOptions;

    protected function setUp(): void
    {
        $this->currencyFormatOptions = new CurrencyFormatOptions();
        $this->numberFormatOptions   = new NumberFormatOptions();
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     * @dataProvider dataProviderRequiredLocales
     */
    public function testCurrencyDefaultRequiredLocales(string $currencyCode, string $locale, string $expected): void
    {
        $options   = (new CurrencyFormatOptions())->setCurrencyCode($currencyCode)->setLocale($locale);
        $formatter = new NumberFormatService($options, $this->numberFormatOptions);

        static::assertSame($expected, $formatter->currency(1234.5678));
    }

    /**
     * @return Generator<string, array<string>>
     */
    public static function dataProviderRequiredLocales(): Generator
    {
        yield "nl_NL, EUR" => ["EUR", "nl_NL", sprintf("€%s1.234,57", self::NBSP)];
        yield "nl_NL, USD" => ["USD", "nl_NL", sprintf("US$%s1.234,57", self::NBSP)];
        yield "nl_BE, EUR" => ["EUR", "nl_BE", sprintf("€%s1.234,57", self::NBSP)];
        yield "nl_BE, USD" => ["USD", "nl_BE", sprintf("US$%s1.234,57", self::NBSP)];
        yield "fr_BE, EUR" => ["EUR", "fr_BE", sprintf("1%s234,57%s€", self::NNBSP, self::NBSP)];
        yield "fr_BE, USD" => ["USD", "fr_BE", sprintf("1%s234,57%s\$US", self::NNBSP, self::NBSP)];
        yield "en_IE, EUR" => ["EUR", "en_IE", "€1,234.57"];
        yield "en_IE, USD" => ["USD", "en_IE", "US$1,234.57"];
        yield "en_GB, GBP" => ["GBP", "en_GB", "£1,234.57"];
        yield "en_GB, EUR" => ["EUR", "en_GB", "€1,234.57"];
        yield "sv_SE, SEK" => ["SEK", "sv_SE", sprintf("1%s234,57%skr", self::NBSP, self::NBSP)];
        yield "sv_SE, EUR" => ["EUR", "sv_SE", sprintf("1%s234,57%s€", self::NBSP, self::NBSP)];
        yield "pl_PL, PLN" => ["PLN", "pl_PL", sprintf("1%s234,57%szł", self::NBSP, self::NBSP)];
        yield "pl_PL, EUR" => ["EUR", "pl_PL", sprintf("1%s234,57%s€", self::NBSP, self::NBSP)];
        yield "es_ES, EUR" => ["EUR", "es_ES", sprintf("1.234,57%s€", self::NBSP)];
        yield "es_ES, SEK" => ["SEK", "es_ES", sprintf("1.234,57%sSEK", self::NBSP)];
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     * @covers ::getNumberFormatter
     */
    public function testCurrencyWithHideEmptyDecimals(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())
            ->setLocale('nl_NL')
            ->setSymbol(false)
            ->setCurrencyCode('EUR')
            ->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);

        static::assertSame('1.234,57', $formatter->currency(1234.567));
        static::assertSame('1.234,50', $formatter->currency(1234.5));
        static::assertSame('-1.234,50', $formatter->currency(-1234.5));
        static::assertSame('1.234', $formatter->currency(1234.0));
        static::assertSame('-1.234', $formatter->currency(-1234.0));

        static::assertSame('1.234,57', $formatter->currency(new Money(123457, new Currency('EUR'))));
        static::assertSame('1.234,50', $formatter->currency(new Money(123450, new Currency('EUR'))));
        static::assertSame('1.234', $formatter->currency(new Money(123400, new Currency('EUR'))));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyShouldFormatAccordingToDefaults(): void
    {
        $options   = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale("es_ES");
        $formatter = new NumberFormatService($options, $this->numberFormatOptions);

        static::assertSame('1.234,57' . self::NBSP . '€', $formatter->currency(1234.5678));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyCacheShouldBeInvoked(): void
    {
        $cache = $this->createMock(NumberFormatterCacheInterface::class);
        $cache->expects(static::once())->method('get')->willReturn(new NumberFormatter('nl_NL@currency=EUR', NumberFormatter::CURRENCY));

        $formatter = new NumberFormatService($this->currencyFormatOptions, $this->numberFormatOptions, null, $cache);
        $value     = 1234.56;

        static::assertSame('€ 1.234,56', $formatter->currency($value));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyCacheShouldBeInvokedOnlyOnce(): void
    {
        $options = $this->createPartialMock(CurrencyFormatOptions::class, ['getLocale']);
        $options->expects(static::once())->method('getLocale')->willReturn('nl_NL');

        $formatter = new NumberFormatService($this->currencyFormatOptions, $this->numberFormatOptions);
        $value     = 1234.56;

        // invoke twice, should call options once
        static::assertSame('€ 1.234,56', $formatter->currency($value, $options));
        static::assertSame('€ 1.234,56', $formatter->currency($value, $options));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyWithCustomSettings(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);

        $options = (new CurrencyFormatOptions())->setSymbol(false)->setGrouping(false);
        $result  = $formatter->currency(1234.5678, $options);
        static::assertSame('1234,57', $result);
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyWithCustomCurrencyCode(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);

        $options = (new CurrencyFormatOptions())->setSymbol(true)->setGrouping(false)->setCurrencyCode('PLN');
        $result  = $formatter->currency(1234.5678, $options);
        static::assertSame('1234,57' . self::NBSP . 'PLN', $result);
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyMoneyObjectShouldNotAcceptCustomCurrencyCode(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);
        $value          = new Money(12345678, new Currency('EUR'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can\'t specify custom currency code for Money objects');
        $formatter->currency($value, (new CurrencyFormatOptions())->setCurrencyCode('PLN'));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyFormatMoney(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);
        $value          = new Money(123456, new Currency('SEK'));

        static::assertSame('1.234,56' . self::NBSP . 'SEK', $formatter->currency($value));
    }

    /**
     * @covers ::currency
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencyFormatMoneyWithCustomSettings(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);
        $value          = new Money(123456, new Currency('SEK'));

        $options = (new CurrencyFormatOptions())->setSymbol(false)->setGrouping(false);
        static::assertSame('1234,56', $formatter->currency($value, $options));
    }

    /**
     * @covers ::currencySplit
     * @covers ::formatCurrencyValue
     * @covers ::getCurrencyFormatter
     */
    public function testCurrencySplit(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('nl_NL');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);
        $value          = 1234.56;

        $expected = new Result('€ 1.234,56', '€ ', '', '1.234', '.', '56', ',', '€', 'before');
        $actual   = $formatter->currencySplit($value);
        static::assertEquals($expected, $actual);
    }

    /**
     * @covers ::currencySplit
     */
    public function testCurrencySplitMoneyObjectShouldNotAcceptCustomCurrencyCode(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setCurrencyCode("EUR")->setLocale('es_ES');
        $formatter      = new NumberFormatService($defaultOptions, $this->numberFormatOptions);
        $value          = new Money(12345678, new Currency('EUR'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Can\'t specify custom currency code for Money objects');
        $formatter->currencySplit($value, (new CurrencyFormatOptions())->setCurrencyCode('PLN'));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberWithDefault(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('es_ES')->setGrouping(true);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);
        $value          = 1234.56;

        static::assertSame('1.234,56', $formatter->number($value));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberWithHideEmptyDecimals(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('nl_NL')
            ->setDecimals(2)
            ->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_ALL_OR_NOTHING);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);

        static::assertSame('1.234,57', $formatter->number(1234.567));
        static::assertSame('1.234,50', $formatter->number(1234.5));
        static::assertSame('-1.234,50', $formatter->number(-1234.5));
        static::assertSame('1.234', $formatter->number(1234.0));
        static::assertSame('-1.234', $formatter->number(-1234.0));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberCacheShouldBeInvoked(): void
    {
        $cache = $this->createMock(NumberFormatterCacheInterface::class);
        $cache->expects(static::once())->method('get')->willReturn(new NumberFormatter('nl_NL', NumberFormatter::DECIMAL));

        $formatter = new NumberFormatService($this->currencyFormatOptions, $this->numberFormatOptions, null, $cache);
        $value     = 1234.56;

        static::assertSame('1.234,56', $formatter->number($value));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberCacheShouldBeInvokedOnlyOnce(): void
    {
        $options = $this->createPartialMock(NumberFormatOptions::class, ['getLocale']);
        $options->expects(static::once())->method('getLocale')->willReturn('nl_NL');

        $formatter = new NumberFormatService($this->currencyFormatOptions, $this->numberFormatOptions);
        $value     = 1234.56;

        // invoke twice, should call options once
        static::assertSame('1.234,56', $formatter->number($value, $options));
        static::assertSame('1.234,56', $formatter->number($value, $options));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberWithNegativeZero(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('nl_NL')->setGrouping(true);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);
        $value          = -0.0;

        $options = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        static::assertSame('0,00', $formatter->number($value, $options));
    }

    /**
     * @covers ::number
     * @covers ::getNumberFormatter
     */
    public function testNumberCustomSettings(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('es_ES')->setGrouping(true);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);
        $value          = 1234.56;

        $options = (new NumberFormatOptions())->setDecimals(5)->setGrouping(false)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        static::assertSame('1234,56000', $formatter->number($value, $options));
    }

    /**
     * @covers ::numberSplit
     * @covers ::getNumberFormatter
     */
    public function testNumberSplit(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('nl_NL')->setGrouping(true);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);
        $value          = 1234.56;

        $expected = new Result('1.234,56', '', '', '1.234', '.', '56', ',', null, 'absent');
        $actual   = $formatter->numberSplit($value);
        static::assertEquals($expected, $actual);
    }

    /**
     * @covers ::numberSplit
     * @covers ::getNumberFormatter
     */
    public function testNumberNegativeZero(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale('nl_NL')->setGrouping(true);
        $formatter      = new NumberFormatService($this->currencyFormatOptions, $defaultOptions);
        $value          = -0.0;

        $expected = new Result('0,00', '', '', '0', '.', '00', ',', null, 'absent');

        $options = (new NumberFormatOptions())->setDecimals(2)->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE);
        $actual  = $formatter->numberSplit($value, $options);
        static::assertEquals($expected, $actual);
    }
}
