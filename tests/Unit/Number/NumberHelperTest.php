<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberHelper;
use Generator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberHelper
 */
class NumberHelperTest extends TestCase
{
    /**
     * @covers ::hasDecimals
     * @dataProvider dataProviderDecimals
     *
     * @param int|float $value
     */
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
