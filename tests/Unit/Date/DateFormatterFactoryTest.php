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
        $options = new DateFormatOptions('Y-m-d');
        $options->setLocale();
        $options->setTimezone();
        $options->setPattern();

        $factory = new DateFormatterFactory($options);
        $result = $factory->create($emptyOptions, 'Y-m-d');

        static::assertSame('nl', $result->getLocale());
        static::assertNotFalse($result->getTimeZone());
        static::assertSame('Europe/Stockholm', $result->getTimeZone()->getID());
        static::assertSame(IntlDateFormatter::FULL, $result->getTimeType());
        static::assertSame(IntlDateFormatter::FULL, $result->getDateType());
        static::assertSame(IntlDateFormatter::GREGORIAN, $result->getCalendar());
        static::assertSame('Y-m-d', $result->getPattern());
    }

    public function testNotDefault(): void
    {
        $emptyOptions = new DateFormatOptions('Y-m-d');
        $options = new DateFormatOptions('Y-m-d');
        $options->setLocale('NL');
        $options->setTimezone('Europe/Stockholm');
        $options->setTimeType(1);
        $options->setDateType(2);
        $options->setCalendar(IntlDateFormatter::TRADITIONAL);
        $options->setPattern('Y-m-d');

        $factory = new DateFormatterFactory($emptyOptions);
        $result = $factory->create($options);

        static::assertSame('nl', $result->getLocale());
        static::assertNotFalse($result->getTimeZone());
        static::assertSame('Europe/Stockholm', $result->getTimeZone()->getID());
        static::assertSame(1, $result->getTimeType());
        static::assertSame(2, $result->getDateType());
        static::assertSame(IntlDateFormatter::TRADITIONAL, $result->getCalendar());
        static::assertSame('Y-m-d', $result->getPattern());
    }

    public function testNoDefaultOptions(): void
    {
        $emptyOptions = new DateFormatOptions('Y-m-d');
        $factory = new DateFormatterFactory();
        $result = $factory->create($emptyOptions);

        static::assertSame('nl', $result->getLocale());
        static::assertNotFalse($result->getTimeZone());
        static::assertSame('Europe/Amsterdam', $result->getTimeZone()->getID());
        static::assertSame(IntlDateFormatter::FULL, $result->getTimeType());
        static::assertSame(IntlDateFormatter::FULL, $result->getDateType());
        static::assertSame(IntlDateFormatter::GREGORIAN, $result->getCalendar());
        static::assertSame('Y-m-d', $result->getPattern());
    }
}
