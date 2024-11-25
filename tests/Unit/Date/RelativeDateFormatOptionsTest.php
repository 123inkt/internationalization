<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\RelativeDateFormatOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RelativeDateFormatOptions::class)]
class RelativeDateFormatOptionsTest extends TestCase
{
    public function testOptions(): void
    {
        $options = new RelativeDateFormatOptions(5);
        static::assertSame($options->getRelativeDaysAmount(), 5);
    }
}
