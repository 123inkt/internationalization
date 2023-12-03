<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberHelper;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberHelper::class)]
class NumberHelperTest extends TestCase
{
    /**
     * @param int|float $value
     */
    #[DataProvider('dataProviderDecimals')]
    public function testHasDecimals($value, bool $hasDecimals): void
    {
        static::assertSame($hasDecimals, NumberHelper::hasDecimals($value));
    }

    /**
     * @return Generator<string, array<float|bool>>
     */
    public static function dataProviderDecimals(): Generator
    {
        // test int
        yield 'int: 5' => [5, false];

        // test a range of negative floats
        $value = 0.0;
        for ($i = 0; $i < 1000; $i++) {
            yield '-float: ' . number_format($value, 2) => [$value, $i % 100 !== 0];
            $value -= 0.01;
        }

        // test a range of floats
        $value = 0.0;
        for ($i = 0; $i < 1000; $i++) {
            yield 'float: ' . number_format($value, 2) => [$value, $i % 100 !== 0];
            $value += 0.01;
        }
    }
}
