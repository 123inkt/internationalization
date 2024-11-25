<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\DateFormatterCache;
use IntlDateFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatterCache::class)]
class DateFormatterCacheTest extends TestCase
{
    public function testGet(): void
    {
        $cache = new DateFormatterCache();
        $formatter = new IntlDateFormatter("en");

        // Should set and invoke callback.
        static::assertSame($formatter, $cache->get("foobar", static fn() => $formatter));

        // Should get formatter from cache.
        static::assertSame($formatter, $cache->get("foobar", static fn() => new IntlDateFormatter("en")));
    }
}
