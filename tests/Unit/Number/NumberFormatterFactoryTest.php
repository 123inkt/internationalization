<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberFormatOptions;
use DR\Internationalization\Number\NumberFormatterFactory;
use InvalidArgumentException;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatterFactory
 * @covers ::__construct
 */
class NumberFormatterFactoryTest extends TestCase
{
    private const MINUS = "\xE2\x88\x92";
    private const NBSP  = "\xC2\xA0";

    /**
     * @covers ::create
     */
    public function testCreateLocaleIsRequired(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('unable to format number without a locale');
        (new NumberFormatterFactory(new NumberFormatOptions()))->create(new NumberFormatOptions());
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $defaultOptions = (new NumberFormatOptions())->setLocale("nl_NL");

        // default locale, show all decimals and thousands separator
        $formatter = (new NumberFormatterFactory($defaultOptions))->create(new NumberFormatOptions());
        static::assertSame('2.005', $formatter->format(2005));
        static::assertSame('2.005', $formatter->format(2005.000));
        static::assertSame('2.005,4', $formatter->format(2005.4));
        static::assertSame('2.005,55', $formatter->format(2005.55));
        static::assertSame('-2.005,55', $formatter->format(-2005.55));

        // default locale, 1 decimal, round up, trim decimals
        $options   = (new NumberFormatOptions())->setDecimals(1)->setRounding(NumberFormatter::ROUND_HALFUP);
        $formatter = (new NumberFormatterFactory($defaultOptions))->create($options);
        static::assertSame('2.005', $formatter->format(2005));
        static::assertSame('2.005,5', $formatter->format(2005.54));
        static::assertSame('2.005,6', $formatter->format(2005.55));
        static::assertSame('2.005', $formatter->format(2005.04));
        static::assertSame('-2.005', $formatter->format(-2005.04));

        // default locale, 1 decimal, no thousand separator, floor, trim decimals
        $options   = (new NumberFormatOptions())->setDecimals(1)->setRounding(NumberFormatter::ROUND_FLOOR)->setGrouping(false);
        $formatter = (new NumberFormatterFactory($defaultOptions))->create($options);
        static::assertSame('2005', $formatter->format(2005));
        static::assertSame('2005,5', $formatter->format(2005.54));
        static::assertSame('2005,5', $formatter->format(2005.55));
        static::assertSame('2005', $formatter->format(2005.04));

        // given locale, 1 decimal, ceil, trim decimals
        $options   = (new NumberFormatOptions())
            ->setDecimals(1)
            ->setRounding(NumberFormatter::ROUND_CEILING)
            ->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE)
            ->setLocale('en_GB');
        $formatter = (new NumberFormatterFactory($defaultOptions))->create($options);
        static::assertSame('2,005.0', $formatter->format(2005));
        static::assertSame('2,005.6', $formatter->format(2005.54));
        static::assertSame('2,005.6', $formatter->format(2005.55));
        static::assertSame('2,005.1', $formatter->format(2005.04));
        static::assertSame('-2,005.0', $formatter->format(-2005.04));

        // sv_SE locale has some UTF8 minus sign
        $options   = (new NumberFormatOptions())
            ->setDecimals(1)
            ->setRounding(NumberFormatter::ROUND_CEILING)
            ->setTrimDecimals(NumberFormatOptions::TRIM_DECIMAL_NONE)
            ->setLocale('sv_SE');
        $formatter = (new NumberFormatterFactory($defaultOptions))->create($options);
        static::assertSame(self::MINUS . '2' . self::NBSP . '005,0', $formatter->format(-2005.04));

        // formatter by locale obj
        $formatter = (new NumberFormatterFactory((new NumberFormatOptions())->setLocale('en_GB')))->create(new NumberFormatOptions());
        static::assertSame('2,005', $formatter->format(2005));
    }
}
