<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DateTimeImmutable;
use DateTimeInterface;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\DateFormatterCache;
use DR\Internationalization\Date\DateFormatterCacheInterface;
use DR\Internationalization\Date\DateFormatterFactory;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use DR\Internationalization\Date\RelativeDateFormatterFactory;
use IntlDateFormatter;
use RuntimeException;

class DateFormatService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;

    private DateFormatterCacheInterface $cache;
    private DateFormatterFactory $dateFactory;
    private RelativeDateFormatterFactory $relativeFormatterFactory;

    public function __construct(
        private readonly DateFormatOptions $options,
        ?DateFormatterCacheInterface       $cache = null,
        ?DateFormatterFactory              $dateFactory = null,
        ?RelativeDateFormatterFactory      $relativeFormatterFactory = null
    )
    {
        $this->cache = $cache ?? new DateFormatterCache();
        $this->dateFactory = $dateFactory ?? new DateFormatterFactory();
        $this->relativeFormatterFactory = $relativeFormatterFactory ?? new RelativeDateFormatterFactory();
    }

    /**
     * @param string $pattern format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $pattern, ?DateFormatOptions $options = null): string
    {
        $options = $options ?? $this->options;
        $result = $this->getDateFormatter($options, $pattern)->format(is_string($value) ? (int)strtotime($value) : $value);

        return $this->validateResult($result, $value, $pattern);
    }

    public function formatRelative(
        int|string|DateTimeInterface $value,
        string                       $pattern,
        RelativeDateFormatOptions    $relativeOptions,
        DateFormatOptions            $fallbackOptions
    ): string
    {
        $defaultOptions = new DateFormatOptions($this->options->getLocale(), $this->options->getTimezone());
        $defaultFormattedDate = $this->getDateFormatter($defaultOptions, $pattern)->format($this->getParsedDate($value));

        $result = $this->relativeFormatterFactory->create($this->options->getLocale())->format($this->getParsedDate($value));
        $resultDateTime = new DateTimeImmutable($this->validateResult($defaultFormattedDate, $value, $pattern));

        if ($this->shouldFallbackDate($resultDateTime, $relativeOptions, $defaultFormattedDate, $result)) {
            $result = $this->getDateFormatter($fallbackOptions, $pattern)->format($this->getParsedDate($value));
            return $this->validateResult($result, $value, $pattern);
        }

        return $this->validateResult($result, $value, $pattern);
    }

    private function shouldFallbackDate(
        DateTimeImmutable         $dateTime,
        RelativeDateFormatOptions $relativeOptions,
        string                    $defaultFormattedDate,
        string                    $actualFormattedDate
    ): bool
    {
        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);

        return $dateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT
            || $relativeOptions->getRelativeDaysAmount() === 0
            || $dateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount()
            || $defaultFormattedDate === $actualFormattedDate;
    }

    private function getDateFormatter(DateFormatOptions $options, string $pattern): IntlDateFormatter
    {
        // get or create from cache
        return $this->cache->get((string)$options, fn() => $this->dateFactory->create($options, $pattern));
    }

    private function getParsedDate(int|string|DateTimeInterface $date): int|DateTimeInterface
    {
        return is_string($date) ? (int)strtotime($date) : $date;
    }

    private function validateResult(bool|string|null $result, int|string|DateTimeInterface $value, string $pattern): string
    {
        // @codeCoverageIgnoreStart
        if ($result === false) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new RuntimeException(sprintf('Unable to format date `%s` to format `%s`', $scalarValue, $pattern));
        }
        // @codeCoverageIgnoreEnd

        return $result;
    }
}
