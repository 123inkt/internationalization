<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Currency;

use DR\Internationalization\Currency\CurrencyFormatOptions;
use DR\Internationalization\Currency\CurrencyFormatterFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CurrencyFormatterFactory::class)]
class CurrencyFormatterFactoryTest extends TestCase
{
    private const MINUS = "\xE2\x88\x92";
    private const NBSP  = "\xC2\xA0";

    public function testCreateLocaleIsRequired(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('unable to format currency without a locale');
        (new CurrencyFormatterFactory(new CurrencyFormatOptions()))->create(new CurrencyFormatOptions());
    }

    public function testCreateWithDefaults(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('nl_NL');
        $prefix         = "€" . self::NBSP;

        // default currency, default locale
        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create(new CurrencyFormatOptions());
        static::assertSame($prefix . '2.005,00', $formatter->format(2005));
        static::assertSame($prefix . '2.005,40', $formatter->format(2005.4));
        static::assertSame($prefix . '2.005,56', $formatter->format(2005.555));

        $defaultOptions = (new CurrencyFormatOptions())->setLocale('nl_NL')->setSymbol(false);
        $formatter      = (new CurrencyFormatterFactory($defaultOptions))->create(new CurrencyFormatOptions());
        static::assertSame('2.005,00', $formatter->format(2005));
        static::assertSame('2.005,40', $formatter->format(2005.4));
        static::assertSame('2.005,56', $formatter->format(2005.555));
        static::assertSame('-2.005,56', $formatter->format(-2005.555));
    }

    public function testCreateDefaultCurrencyWithCustomSettings(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('nl_NL');
        $options        = (new CurrencyFormatOptions())->setDecimals(4)->setSymbol(false);

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create($options);
        static::assertSame('-2.005,1235', $formatter->format(-2005.123456));
    }

    public function testCreateSwedishCurrencyWithSymbol(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('sv_SE');

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create(new CurrencyFormatOptions());
        static::assertSame('2' . self::NBSP . '005,00' . self::NBSP . 'kr', $formatter->format(2005));
        static::assertSame('2' . self::NBSP . '005,40' . self::NBSP . 'kr', $formatter->format(2005.4));
        static::assertSame('2' . self::NBSP . '005,56' . self::NBSP . 'kr', $formatter->format(2005.555));
        static::assertSame(self::MINUS . '2' . self::NBSP . '005,56' . self::NBSP . 'kr', $formatter->format(-2005.555));
    }

    public function testCreateSwedishCurrencyWithoutSymbol(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('sv_SE')->setCurrencyCode('SEK');
        $options        = (new CurrencyFormatOptions())->setSymbol(false);

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create($options);
        static::assertSame('2' . self::NBSP . '005,00', $formatter->format(2005));
        static::assertSame('2' . self::NBSP . '005,40', $formatter->format(2005.4));
        static::assertSame('2' . self::NBSP . '005,56', $formatter->format(2005.555));
        static::assertSame(self::MINUS . '2' . self::NBSP . '005,56', $formatter->format(-2005.555));
    }

    public function testCreateIrishCurrency(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('en_IE')->setCurrencyCode('EUR');

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create(new CurrencyFormatOptions());
        static::assertSame("€" . '2,005.00', $formatter->format(2005));
        static::assertSame("€" . '2,005.40', $formatter->format(2005.4));
        static::assertSame("€" . '2,005.56', $formatter->format(2005.555));
    }

    public function testCreateForeignCurrencyWithoutSymbolOrGrouping(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('nl_NL')->setCurrencyCode('SEK');
        $options        = (new CurrencyFormatOptions())->setSymbol(false)->setGrouping(false);

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create($options);

        static::assertSame('2005,00', $formatter->format(2005));
        static::assertSame('2005,40', $formatter->format(2005.4));
        static::assertSame('2005,56', $formatter->format(2005.555));
        static::assertSame('-2005,56', $formatter->format(-2005.555));
    }

    public function testCreateForeignCurrencyWithSymbol(): void
    {
        $defaultOptions = (new CurrencyFormatOptions())->setLocale('nl_NL')->setCurrencyCode('SEK');
        $options        = (new CurrencyFormatOptions())->setSymbol(true)->setGrouping(true);

        $formatter = (new CurrencyFormatterFactory($defaultOptions))->create($options);

        static::assertSame('SEK 2.005,00', $formatter->format(2005));
        static::assertSame('SEK 2.005,40', $formatter->format(2005.4));
        static::assertSame('SEK 2.005,56', $formatter->format(2005.555));
        static::assertSame('SEK -2.005,56', $formatter->format(-2005.555));
    }
}
