<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Internationalization\Number\NumberSeparator;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberSeparator
 */
class NumberSeparatorTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::__construct
     * @covers ::getThousand
     * @covers ::getDecimal
     */
    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(NumberSeparator::class);
    }
}
