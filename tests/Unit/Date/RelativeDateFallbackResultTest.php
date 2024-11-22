<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DR\Internationalization\Date\RelativeDateFallbackResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RelativeDateFallbackResult::class)]
class RelativeDateFallbackResultTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessorPairs(): void
    {
        static::assertAccessorPairs(RelativeDateFallbackResult::class);
    }
}
