<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTimeImmutable;

class RelativeDateFallbackService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;

    public function shouldFallback(
        DateTimeImmutable         $dateTime,
        RelativeDateFormatOptions $relativeOptions,
        string|false              $defaultFormattedDate,
        string|false              $actualFormattedDate
    ): bool {
        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        return $dateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT
            || $relativeOptions->getRelativeDaysAmount() === 0
            || $dateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount()
            || $defaultFormattedDate === $actualFormattedDate;
    }
}
