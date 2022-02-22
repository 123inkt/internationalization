<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Money;

use DR\Internationalization\Money\MoneyParseException;
use DR\Internationalization\Money\MoneyProvider;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Money\MoneyProvider
 */
class MoneyProviderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::parse
     * @dataProvider provideFloat
     * @throws MoneyParseException
     */
    public function testParse(string $expected, string $price, string $currencyCode): void
    {
        $provider = new MoneyProvider($currencyCode);
        $money    = $provider->parse($price);

        static::assertSame($expected, $money->getAmount());
    }

    /**
     * @covers ::getMoney
     * @dataProvider provideMoney
     *
     * @param int|string $amount
     */
    public function testGetMoney(string $expected, $amount, string $currencyCode): void
    {
        $provider = new MoneyProvider($currencyCode);
        $money = $provider->getMoney($amount, $currencyCode);
        static::assertSame($expected, $money->getAmount());
    }

    /**
     * @covers ::parse
     * @dataProvider provideInvalidParse
     *
     * @throws MoneyParseException
     */
    public function testFailedParse(string $price, string $currencyCode): void
    {
        $this->expectException(MoneyParseException::class);
        $this->expectExceptionMessage('Amount: ' . $price . ' and currencyCode: ' . $currencyCode);
        $provider = new MoneyProvider($currencyCode);
        $provider->parse($price)->getAmount();
    }

    /**
     * @return array<array<string, int|string>>
     */
    public function provideMoney(): array
    {
        return [
            [
                'expected'     => '34299',
                'price'        => 34299,
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '3',
                'price'        => 3,
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '0',
                'price'        => 0,
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '1031',
                'price'        => 1031,
                'currencyCode' => 'SEK'
            ]
        ];
    }

    /**
     * @return array<array<string, string>>
     */
    public function provideFloat(): array
    {
        return [
            [
                'expected'     => '299',
                'price'        => '2.99',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '34299',
                'price'        => '342.99',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '990',
                'price'        => '9.90',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '0',
                'price'        => '0.0',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '-2001',
                'price'        => '-20.01',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '-2001',
                'price'        => '-20,01',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '124',
                'price'        => '1.235',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '123',
                'price'        => '1.234',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '2520024',
                'price'        => '25.200,24',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '5265',
                'price'        => '52.65',
                'currencyCode' => 'PLN'
            ],
            [
                'expected'     => '1031',
                'price'        => '10.31',
                'currencyCode' => 'SEK'
            ],
            [
                'expected'     => '31',
                'price'        => '0.31',
                'currencyCode' => 'SEK'
            ],
            [
                'expected'     => '3100',
                'price'        => '31',
                'currencyCode' => 'SEK'
            ],
            [
                'expected'     => '299',
                'price'        => '002.99',
                'currencyCode' => 'EUR'
            ],
            [
                'expected'     => '-10',
                'price'        => '-00.1',
                'currencyCode' => 'EUR'
            ]
        ];
    }

    /**
     * @return array<array<string, string>>
     */
    public function provideInvalidParse(): array
    {
        return [

            [
                'price'        => '10EU10',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => '+500',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => '0x1A',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => '0b11111111',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => '1_234_567',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => '5.0E+19',
                'currencyCode' => 'EUR'
            ],
            [
                'price'        => 'true',
                'currencyCode' => 'PLN'
            ],
            [
                'price'        => '0x10000',
                'currencyCode' => 'SEK'
            ]
        ];
    }
}
