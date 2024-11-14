<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\RelativeDateFormatterFactory;
use IntlDateFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RelativeDateFormatterFactory::class)]
class RelativeDateFormatterFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new RelativeDateFormatterFactory();
        $result = $factory->create('NL');

        static::assertSame('', $result->getLocale());
        static::assertSame(IntlDateFormatter::RELATIVE_FULL, $result->getDateType());
        static::assertSame(IntlDateFormatter::NONE, $result->getTimeType());
        static::assertSame('UTC', $result->getTimeZone()->getID());
        static::assertSame(IntlDateFormatter::GREGORIAN, $result->getCalendar());
        static::assertSame('EEEE d MMMM y', $result->getPattern());
    }
}
