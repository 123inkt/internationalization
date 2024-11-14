<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\DateFormatterFactory;
use IntlDateFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatterFactory::class)]
class DateFormatterFactoryTest extends TestCase
{
    public function testDefault(): void
    {
        $emptyOptions = new DateFormatOptions('NL', 'Europe/Stockholm');

        $factory = new DateFormatterFactory();
        $result = $factory->create($emptyOptions, 'Y-m-d');

        static::assertSame('nl', $result->getLocale());
        static::assertNotFalse($result->getTimeZone());
        static::assertSame('Europe/Stockholm', $result->getTimeZone()->getID());
        static::assertSame(IntlDateFormatter::FULL, $result->getTimeType());
        static::assertSame(IntlDateFormatter::FULL, $result->getDateType());
        static::assertSame(IntlDateFormatter::GREGORIAN, $result->getCalendar());
        static::assertSame('Y-m-d', $result->getPattern());
    }
}
