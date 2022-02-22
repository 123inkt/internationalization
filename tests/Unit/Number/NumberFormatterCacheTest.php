<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Number;

use DR\Internationalization\Number\NumberFormatterCache;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\Number\NumberFormatterCache
 */
class NumberFormatterCacheTest extends TestCase
{
    /**
     * @covers ::get
     */
    public function testGet(): void
    {
        $cache     = new NumberFormatterCache();
        $formatter = new NumberFormatter("en", NumberFormatter::DECIMAL);

        // should set and invoke callback
        static::assertSame($formatter, $cache->get("foobar", static fn() => $formatter));

        // should get formatter from cache
        static::assertSame($formatter, $cache->get("foobar", static fn() => new NumberFormatter("en", NumberFormatter::DECIMAL)));
    }
}
