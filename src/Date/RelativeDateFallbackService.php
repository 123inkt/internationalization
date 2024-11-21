<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTimeImmutable;
use DateTimeInterface;

class RelativeDateFallbackService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;

    public function shouldFallback(DateTimeInterface $dateTime, RelativeDateFormatOptions $relativeOptions): bool
    {
        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        return $dateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT
            || $relativeOptions->getRelativeDaysAmount() === 0
            || $relativeOptions->getRelativeDaysAmount() === null
            || $dateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount();
    }
}
