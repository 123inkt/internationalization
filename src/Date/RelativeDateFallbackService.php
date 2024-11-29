<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTimeImmutable;
use DateTimeInterface;
use RuntimeException;

/**
 * @internal
 */
class RelativeDateFallbackService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;
    private RelativeDateFormatterFactory $relativeFormatterFactory;

    public function __construct(?RelativeDateFormatterFactory $relativeFormatterFactory = null)
    {
        $this->relativeFormatterFactory = $relativeFormatterFactory ?? new RelativeDateFormatterFactory();
    }

    public function getFallbackResult(
        DateFormatOptions         $dateFormatOptions,
        DateTimeInterface         $dateTime,
        RelativeDateFormatOptions $relativeOptions
    ): RelativeDateFallbackResult {
        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        if ($dateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT
            || $relativeOptions->getRelativeDaysAmount() === 0
            || $relativeOptions->getRelativeDaysAmount() === null
            || $dateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount()
        ) {
            return new RelativeDateFallbackResult(true);
        }

        $relativeDateFormatter = $this->relativeFormatterFactory->createRelativeFull($dateFormatOptions);
        $fullDateFormatter = $this->relativeFormatterFactory->createFull($dateFormatOptions);

        $relativeDate = $relativeDateFormatter->format($dateTime);
        $fullDate = $fullDateFormatter->format($dateTime);

        if ($relativeDate === false) {
            throw new RuntimeException(
                sprintf(
                    'An error occurred while trying to parse the relative date. Error code: %s, %s',
                    $relativeDateFormatter->getErrorCode(),
                    $relativeDateFormatter->getErrorMessage()
                )
            );
        }

        if ($relativeDate === $fullDate) {
            return new RelativeDateFallbackResult(true);
        }

        return new RelativeDateFallbackResult(false, $relativeDate);
    }
}
