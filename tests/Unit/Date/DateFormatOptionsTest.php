<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\DateFormatOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatOptions::class)]
class DateFormatOptionsTest extends TestCase
{
    public function test(): void
    {
        $options = new DateFormatOptions('nl_NL', 'Europe/Amsterdam');
        $options->setDateType(1);
        $options->setTimeType(2);
        $options->setCalendar(1);
        $options->setLocale('en_GB');
        $options->setTimezone('Europe/Amsterdam');

        $serializedData = 'a:5:{s:6:"locale";s:5:"en_GB";s:8:"timezone";s:16:' .
            '"Europe/Amsterdam";s:8:"dateType";i:1;s:8:"timeType";i:2;s:8:"calendar";i:1;}';

        static::assertSame(1, $options->getDateType());
        static::assertSame(2, $options->getTimeType());
        static::assertSame(1, $options->getCalendar());
        static::assertSame('en_GB', $options->getLocale());
        static::assertSame('Europe/Amsterdam', $options->getTimezone());
        static::assertSame("date:" . $serializedData, (string)$options);
    }
}
