<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Time;

use DateTimeImmutable;
use DR\Internationalization\Time\Time;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Time::class)]
class TimeTest extends TestCase
{
    public function testAddHours(): void
    {
        static::assertSame('00:00:00', (string)(new Time(0, 0, 0))->addHours(0));
        static::assertSame('02:00:00', (string)(new Time(0, 0, 0))->addHours(2));
        static::assertSame('01:00:00', (string)(new Time(20, 0, 0))->addHours(5));
    }

    public function testSubtractHours(): void
    {
        static::assertSame('23:00:00', (string)(new Time(23, 0, 0))->subtractHours(0));
        static::assertSame('21:00:00', (string)(new Time(23, 0, 0))->subtractHours(2));
        static::assertSame('22:00:00', (string)(new Time(3, 0, 0))->subtractHours(5));
    }

    public function testAddMinutes(): void
    {
        static::assertSame('00:00:00', (string)(new Time(0, 0, 0))->addMinutes(0));
        static::assertSame('00:30:00', (string)(new Time(0, 0, 0))->addMinutes(30));
        static::assertSame('01:20:00', (string)(new Time(0, 0, 0))->addMinutes(80));
        static::assertSame('00:10:00', (string)(new Time(23, 30, 0))->addMinutes(40));
        static::assertSame('00:20:00', (string)(new Time(0, 0, 0))->addMinutes(1460));
    }

    public function testSubtractMinutes(): void
    {
        static::assertSame('00:00:00', (string)(new Time(0, 0, 0))->subtractMinutes(0));
        static::assertSame('23:30:00', (string)(new Time(0, 0, 0))->subtractMinutes(30));
        static::assertSame('22:40:00', (string)(new Time(0, 0, 0))->subtractMinutes(80));
        static::assertSame('23:50:00', (string)(new Time(0, 30, 0))->subtractMinutes(40));
        static::assertSame('23:40:00', (string)(new Time(0, 0, 0))->subtractMinutes(1460));
    }

    public function testAddSeconds(): void
    {
        static::assertSame('00:00:00', (string)(new Time(0, 0, 0))->addSeconds(0));
        static::assertSame('00:00:30', (string)(new Time(0, 0, 0))->addSeconds(30));
        static::assertSame('00:01:20', (string)(new Time(0, 0, 0))->addSeconds(80));
        static::assertSame('01:01:20', (string)(new Time(0, 0, 0))->addSeconds(3680));
        static::assertSame('00:00:30', (string)(new Time(0, 0, 0))->addSeconds(86430));
    }

    public function testSubtractSeconds(): void
    {
        static::assertSame('00:00:00', (string)(new Time(0, 0, 0))->subtractSeconds(0));
        static::assertSame('00:00:20', (string)(new Time(0, 0, 50))->subtractSeconds(30));
        static::assertSame('00:00:50', (string)(new Time(0, 1, 30))->subtractSeconds(40));
        static::assertSame('23:59:20', (string)(new Time(0, 0, 0))->subtractSeconds(40));
        static::assertSame('23:59:30', (string)(new Time(0, 0, 0))->subtractSeconds(86430));
    }

    public function testFormat(): void
    {
        $time = new Time(12, 34, 56);
        static::assertSame('12:34:56', $time->format('H:i:s'));
        static::assertSame('12:34 PM', $time->format('g:i A'));
    }

    public function testToDateTime(): void
    {
        static::assertSame('12:34:56', (new Time(12, 34, 56))->toDateTime()->format('H:i:s'));
        static::assertSame('12:34:56', (new Time(12, 34, 56))->toDateTime(new DateTimeImmutable())->format('H:i:s'));
    }

    public function testToString(): void
    {
        static::assertSame('00:00:00', (string)new Time(0, 0, 0));
        static::assertSame('12:12:12', (string)new Time(12, 12, 12));
    }

    public function testFromString(): void
    {
        static::assertSame('12:34:56', (string)Time::fromString('12:34:56'));
        static::assertSame('12:34:00', (string)Time::fromString('12:34'));
        static::assertSame('12:00:00', (string)Time::fromString('12'));
    }

    public function testFromSeconds(): void
    {
        static::assertSame('00:00:20', (string)Time::fromSeconds(20));
        static::assertSame('00:01:20', (string)Time::fromSeconds(80));
        static::assertSame('01:01:20', (string)Time::fromSeconds(3680));
    }
}
