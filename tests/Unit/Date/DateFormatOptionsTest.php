<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DigitalRevolution\AccessorPairConstraint\Test\AbstractDtoTestCase;
use DR\Internationalization\Date\DateFormatOptions;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateFormatOptions::class)]
class DateFormatOptionsTest extends AbstractDtoTestCase
{
    public function testToString(): void
    {
        $options = new DateFormatOptions('y-m-d');
        $options->setLocale('NL');
        $options->setTimezone('Europe/Amsterdam');
        $options->setDateType(1);
        $options->setTimeType(2);
        $options->setCalendar(3);

        $serializedData = serialize([
            'locale' => $options->getLocale(),
            'timezone' => $options->getTimezone(),
            'pattern' => $options->getPattern(),
            'dateType' => $options->getDateType(),
            'timeType' => $options->getTimeType(),
            'calendar' => $options->getCalendar(),
        ]);

        static::assertSame("date:" . $serializedData, (string)$options);
    }
}
