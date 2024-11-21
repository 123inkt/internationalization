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
        $options = new DateFormatOptions('NL', 'Europe/Amsterdam');
        $options->setDateType(1);
        $options->setTimeType(2);
        $options->setCalendar(1);
        $options->setLocale('en');
        $options->setTimezone('Europe/Amsterdam');

        $serializedData = serialize([
            'locale' => $options->getLocale(),
            'timezone' => $options->getTimezone(),
            'dateType' => $options->getDateType(),
            'timeType' => $options->getTimeType(),
            'calendar' => $options->getCalendar(),
        ]);

        static::assertSame("date:" . $serializedData, (string)$options);
    }
}
