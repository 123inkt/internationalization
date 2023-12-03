<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberFormatterSplitter;
use DR\Internationalization\Number\NumberFormatterSplitterResult as Result;
use Generator;
use InvalidArgumentException;
use NumberFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberFormatterSplitter::class)]
class NumberFormatterSplitterTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testSplitInvalidFormat(): void
    {
        $formatter = new NumberFormatter('nl_NL@currency=EUR', NumberFormatter::CURRENCY);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('NumberFormatter type: ' . -1 . ' is not supported (yet)');
        new NumberFormatterSplitter($formatter, -1);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSplitWithoutSymbolInPrefixOrSuffix(): void
    {
        $formatter = new NumberFormatter('nl_NL@currency=EUR', NumberFormatter::CURRENCY);
        $formatter->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, '');
        $formatter->setTextAttribute(NumberFormatter::POSITIVE_SUFFIX, '');

        $expected = new Result('1234,56', '', '', '1234', '.', '56', ',', null, 'absent');
        $actual   = (new NumberFormatterSplitter($formatter, NumberFormatter::CURRENCY))->split('1234,56', true);
        static::assertResult($expected, $actual);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[DataProvider('dataProviderCurrency')]
    public function testSplitCurrency(string $locale, string $currencyCode, float $value, Result $result): void
    {
        $formatter = new NumberFormatter($locale . '@currency=' . $currencyCode, NumberFormatter::CURRENCY);
        $splitter  = new NumberFormatterSplitter($formatter, NumberFormatter::CURRENCY);

        static::assertResult($result, $splitter->split($formatter->format($value), $value >= 0));
    }

    /**
     * @return Generator<string, array<string|float|Result>>
     */
    public static function dataProviderCurrency(): Generator
    {
        yield "es_ES, SEK" => ['es_ES', 'SEK', 1234.567, new Result('1.234,57 SEK', '', ' SEK', '1.234', '.', '57', ',', 'SEK', 'after')];
        yield "es_ES, -EUR" => ['es_ES', 'EUR', -1234.567, new Result('-1.234,57 €', '-', ' €', '1.234', '.', '57', ',', '€', 'after')];
        yield "es_ES, EUR" => ['es_ES', 'EUR', 1234.567, new Result('1.234,57 €', '', ' €', '1.234', '.', '57', ',', '€', 'after')];

        yield "pl_PL, EUR" => ['pl_PL', 'EUR', 1234.567, new Result('1 234,57 €', '', ' €', '1 234', ' ', '57', ',', '€', 'after')];
        yield "pl_PL, -PLN" => ['pl_PL', 'PLN', -1234.567, new Result('-1 234,57 zł', '-', ' zł', '1 234', ' ', '57', ',', 'zł', 'after')];
        yield "pl_PL, PLN" => ['pl_PL', 'PLN', 1234.567, new Result('1 234,57 zł', '', ' zł', '1 234', ' ', '57', ',', 'zł', 'after')];

        yield "en_IE, SEK" => ['en_IE', 'SEK', -1234.567, new Result('-SEK 1,234.57', '-SEK', '', ' 1,234', ',', '57', '.', 'SEK', 'before')];
        yield "en_IE, -EUR" => ['en_IE', 'EUR', -1234.567, new Result('-€1,234.57', '-€', '', '1,234', ',', '57', '.', '€', 'before')];
        yield "en_IE, EUR" => ['en_IE', 'EUR', 1234.567, new Result('€1,234.57', '€', '', '1,234', ',', '57', '.', '€', 'before')];

        yield "sv_SE, EUR" => ['sv_SE', 'EUR', 1234.567, new Result('1 234,57 €', '', ' €', '1 234', ' ', '57', ',', '€', 'after')];
        yield "sv_SE, -SEK" => ['sv_SE', 'SEK', -1234.567, new Result('−1 234,57 kr', '−', ' kr', '1 234', ' ', '57', ',', 'kr', 'after')];
        yield "sv_SE, SEK" => ['sv_SE', 'SEK', 1234.567, new Result('1 234,57 kr', '', ' kr', '1 234', ' ', '57', ',', 'kr', 'after')];

        yield "fr_BE, SEK" => ['fr_BE', 'SEK', 1234.567, new Result('1 234,57 SEK', '', ' SEK', '1 234', ' ', '57', ',', 'SEK', 'after')];
        yield "fr_BE, -EUR" => ['fr_BE', 'EUR', -1234.567, new Result('-1 234,57 €', '-', ' €', '1 234', ' ', '57', ',', '€', 'after')];
        yield "fr_BE, EUR" => ['fr_BE', 'EUR', 1234.567, new Result('1 234,57 €', '', ' €', '1 234', ' ', '57', ',', '€', 'after')];

        yield "nl_BE, SEK" => ['nl_BE', 'SEK', 1234.567, new Result('SEK 1.234,57', 'SEK ', '', '1.234', '.', '57', ',', 'SEK', 'before')];
        yield "nl_BE, -EUR" => ['nl_BE', 'EUR', -1234.567, new Result('€ -1.234,57', '€ -', '', '1.234', '.', '57', ',', '€', 'before')];
        yield "nl_BE, EUR" => ['nl_BE', 'EUR', 1234.567, new Result('€ 1.234,57', '€ ', '', '1.234', '.', '57', ',', '€', 'before')];

        yield "nl_NL, SEK" => ['nl_NL', 'SEK', 1234.567, new Result('SEK 1.234,57', 'SEK ', '', '1.234', '.', '57', ',', 'SEK', 'before')];
        yield "nl_NL, -EUR" => ['nl_NL', 'EUR', -1234.567, new Result('€ -1.234,57', '€ -', '', '1.234', '.', '57', ',', '€', 'before')];
        yield "nl_NL, EUR" => ['nl_NL', 'EUR', 1234.567, new Result('€ 1.234,57', '€ ', '', '1.234', '.', '57', ',', '€', 'before')];
    }

    /**
     * @throws InvalidArgumentException
     */
    #[DataProvider('dataProviderNumber')]
    public function testSplitNumber(string $locale, float $value, Result $result): void
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $splitter  = new NumberFormatterSplitter($formatter, NumberFormatter::DECIMAL);

        static::assertResult($result, $splitter->split($formatter->format($value), $value >= 0));
    }

    /**
     * @return Generator<string, array<string|float|Result>>
     */
    public static function dataProviderNumber(): Generator
    {
        yield "es_ES, -" => ['es_ES', -1234.567, new Result('-1.234,567', '-', '', '1.234', '.', '567', ',', null, 'absent')];
        yield "es_ES, +" => ['es_ES', 1234.567, new Result('1.234,567', '', '', '1.234', '.', '567', ',', null, 'absent')];

        yield "pl_PL, -" => ['pl_PL', -1234.567, new Result('-1 234,567', '-', '', '1 234', ' ', '567', ',', null, 'absent')];
        yield "pl_PL, +" => ['pl_PL', 1234.567, new Result('1 234,567', '', '', '1 234', ' ', '567', ',', null, 'absent')];

        yield "en_IE, -" => ['en_IE', -1234.567, new Result('-1,234.567', '-', '', '1,234', ',', '567', '.', null, 'absent')];
        yield "en_IE, +" => ['en_IE', 1234.567, new Result('1,234.567', '', '', '1,234', ',', '567', '.', null, 'absent')];

        yield "sv_SE, -" => ['sv_SE', -1234.567, new Result('−1 234,567', '−', '', '1 234', ' ', '567', ',', null, 'absent')];
        yield "sv_SE, +" => ['sv_SE', 1234.567, new Result('1 234,567', '', '', '1 234', ' ', '567', ',', null, 'absent')];

        yield "fr_BE, -" => ['fr_BE', -1234.567, new Result('-1 234,567', '-', '', '1 234', ' ', '567', ',', null, 'absent')];
        yield "fr_BE, +" => ['fr_BE', 1234.567, new Result('1 234,567', '', '', '1 234', ' ', '567', ',', null, 'absent')];

        yield "nl_BE, -" => ['nl_BE', -1234.567, new Result('-1.234,567', '-', '', '1.234', '.', '567', ',', null, 'absent')];
        yield "nl_BE, +" => ['nl_BE', 1234.567, new Result('1.234,567', '', '', '1.234', '.', '567', ',', null, 'absent')];

        yield "nl_NL, -" => ['nl_NL', -1234.567, new Result('-1.234,567', '-', '', '1.234', '.', '567', ',', null, 'absent')];
        yield "nl_NL, +" => ['nl_NL', 1234.567, new Result('1.234,567', '', '', '1.234', '.', '567', ',', null, 'absent')];

        yield "nl_NL, no-decimals" => ['nl_NL', 1234, new Result('1.234', '', '', '1.234', '.', '', ',', null, 'absent')];
    }

    private static function assertResult(Result $expected, Result $actual): void
    {
        static::assertSame($expected->getSymbol(), $actual->getSymbol());
        static::assertSame($expected->getValue(), self::replaceWhiteSpace($actual->getValue()));
        static::assertSame($expected->getPrefix(), self::replaceWhiteSpace($actual->getPrefix()));
        static::assertSame($expected->getSuffix(), self::replaceWhiteSpace($actual->getSuffix()));
        static::assertSame($expected->getInteger(), self::replaceWhiteSpace($actual->getInteger()));
        static::assertSame($expected->getDecimals(), $actual->getDecimals());
        static::assertSame($expected->getGroupingSeparator(), self::replaceWhiteSpace($actual->getGroupingSeparator()));
        static::assertSame($expected->getDecimalSeparator(), $actual->getDecimalSeparator());
        static::assertSame($expected->getSymbolPosition(), $actual->getSymbolPosition());
    }

    /**
     * Replace utf8 nbsp with regular space to simplify comparisons
     */
    private static function replaceWhiteSpace(string $value): string
    {
        return str_replace(["\xE2\x80\xAF", "\xC2\xA0"], " ", $value);
    }
}
