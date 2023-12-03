<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Internationalization\Number\NumberSeparator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberSeparator::class)]
class NumberSeparatorTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(NumberSeparator::class);
    }
}
