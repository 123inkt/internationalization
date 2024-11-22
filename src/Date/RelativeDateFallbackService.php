<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTimeImmutable;
use DateTimeInterface;

class RelativeDateFallbackService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;
    private RelativeDateFormatterFactory $relativeFormatterFactory;

    public function __construct(private readonly string $locale, ?RelativeDateFormatterFactory $relativeFormatterFactory = null)
    {
        $this->relativeFormatterFactory = $relativeFormatterFactory ?? new RelativeDateFormatterFactory();
    }

    public function getFallbackResult(DateTimeInterface $dateTime, RelativeDateFormatOptions $relativeOptions): RelativeDateFallbackResult
    {
        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        if ($dateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT
            || $relativeOptions->getRelativeDaysAmount() === 0
            || $relativeOptions->getRelativeDaysAmount() === null
            || $dateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount()
        ) {
            return new RelativeDateFallbackResult(true);
        }

        $relativeFullDate = $this->relativeFormatterFactory->createRelativeFull($this->locale)->format($dateTime);
        $fullDate = $this->relativeFormatterFactory->createFull($this->locale)->format($dateTime);

        if ($relativeFullDate === $fullDate) {
            return new RelativeDateFallbackResult(true);
        }

        return new RelativeDateFallbackResult(false, $relativeFullDate);
    }
}
