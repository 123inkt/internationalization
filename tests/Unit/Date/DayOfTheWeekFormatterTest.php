<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\DayOfTheWeekFormatter;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type DayOfTheWeek from DayOfTheWeekFormatter
 */
#[CoversClass(DayOfTheWeekFormatter::class)]
class DayOfTheWeekFormatterTest extends TestCase
{
    /**
     * @phpstan-param DayOfTheWeek $dayOfWeek
     * @throws Exception
     */
    #[DataProvider('formatDayOfTheWeekDataProvider')]
    public function testFormat(string $locale, int $dayOfWeek, string $expected): void
    {
        $formatter = new DayOfTheWeekFormatter($locale);
        static::assertSame($expected, $formatter->format($dayOfWeek));
    }

    /**
     * @phpstan-param DayOfTheWeek $dayOfWeek
     * @throws Exception
     */
    #[DataProvider('formatDayOfTheWeekInvalidDataProvider')]
    public function testFormatInvalid(int $dayOfWeek, string $expectedMessage): void
    {
        $formatter = new DayOfTheWeekFormatter('en_GB');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);
        $formatter->format($dayOfWeek);
    }

    /**
     * @throws Exception
     */
    public function testFormatInvalidCustomLocale(): void
    {
        $formatter = new DayOfTheWeekFormatter('en_GB');
        static::assertSame('maandag', $formatter->format(DayOfTheWeekFormatter::MONDAY, 'nl_NL'));
    }

    /**
     * @return array<string, array>
     */
    public static function formatDayOfTheWeekDataProvider(): array
    {
        return [
            'EN-Mon' => ['en_GB', DayOfTheWeekFormatter::MONDAY, 'Monday'],
            'EN-Tue' => ['en_GB', DayOfTheWeekFormatter::TUESDAY, 'Tuesday'],
            'EN-Wed' => ['en_GB', DayOfTheWeekFormatter::WEDNESDAY, 'Wednesday'],
            'EN-Thu' => ['en_GB', DayOfTheWeekFormatter::THURSDAY, 'Thursday'],
            'EN-Fri' => ['en_GB', DayOfTheWeekFormatter::FRIDAY, 'Friday'],
            'EN-Sat' => ['en_GB', DayOfTheWeekFormatter::SATURDAY, 'Saturday'],
            'EN-Sun' => ['en_GB', DayOfTheWeekFormatter::SUNDAY, 'Sunday'],

            'NL-Mon' => ['nl_NL', DayOfTheWeekFormatter::MONDAY, 'maandag'],
            'NL-Tue' => ['nl_NL', DayOfTheWeekFormatter::TUESDAY, 'dinsdag'],
            'NL-Wed' => ['nl_NL', DayOfTheWeekFormatter::WEDNESDAY, 'woensdag'],
            'NL-Thu' => ['nl_NL', DayOfTheWeekFormatter::THURSDAY, 'donderdag'],
            'NL-Fri' => ['nl_NL', DayOfTheWeekFormatter::FRIDAY, 'vrijdag'],
            'NL-Sat' => ['nl_NL', DayOfTheWeekFormatter::SATURDAY, 'zaterdag'],
            'NL-Sun' => ['nl_NL', DayOfTheWeekFormatter::SUNDAY, 'zondag'],

            'ES-Mon' => ['es_ES', DayOfTheWeekFormatter::MONDAY, 'lunes'],
            'ES-Tue' => ['es_ES', DayOfTheWeekFormatter::TUESDAY, 'martes'],
            'ES-Wed' => ['es_ES', DayOfTheWeekFormatter::WEDNESDAY, 'miércoles'],
            'ES-Thu' => ['es_ES', DayOfTheWeekFormatter::THURSDAY, 'jueves'],
            'ES-Fri' => ['es_ES', DayOfTheWeekFormatter::FRIDAY, 'viernes'],
            'ES-Sat' => ['es_ES', DayOfTheWeekFormatter::SATURDAY, 'sábado'],
            'ES-Sun' => ['es_ES', DayOfTheWeekFormatter::SUNDAY, 'domingo'],

            'SV-Mon' => ['sv_SE', DayOfTheWeekFormatter::MONDAY, 'måndag'],
            'SV-Tue' => ['sv_SE', DayOfTheWeekFormatter::TUESDAY, 'tisdag'],
            'SV-Wed' => ['sv_SE', DayOfTheWeekFormatter::WEDNESDAY, 'onsdag'],
            'SV-Thu' => ['sv_SE', DayOfTheWeekFormatter::THURSDAY, 'torsdag'],
            'SV-Fri' => ['sv_SE', DayOfTheWeekFormatter::FRIDAY, 'fredag'],
            'SV-Sat' => ['sv_SE', DayOfTheWeekFormatter::SATURDAY, 'lördag'],
            'SV-Sun' => ['sv_SE', DayOfTheWeekFormatter::SUNDAY, 'söndag'],

            'PL-Mon' => ['pl_PL', DayOfTheWeekFormatter::MONDAY, 'poniedziałek'],
            'PL-Tue' => ['pl_PL', DayOfTheWeekFormatter::TUESDAY, 'wtorek'],
            'PL-Wed' => ['pl_PL', DayOfTheWeekFormatter::WEDNESDAY, 'środa'],
            'PL-Thu' => ['pl_PL', DayOfTheWeekFormatter::THURSDAY, 'czwartek'],
            'PL-Fri' => ['pl_PL', DayOfTheWeekFormatter::FRIDAY, 'piątek'],
            'PL-Sat' => ['pl_PL', DayOfTheWeekFormatter::SATURDAY, 'sobota'],
            'PL-Sun' => ['pl_PL', DayOfTheWeekFormatter::SUNDAY, 'niedziela'],
        ];
    }

    /**
     * @return array[]
     */
    public static function formatDayOfTheWeekInvalidDataProvider(): array
    {
        return [
            [0, '0 is not a valid ISO-8601 numeric representation of the day of the week.'],
            [8, '8 is not a valid ISO-8601 numeric representation of the day of the week.'],
        ];
    }
}
