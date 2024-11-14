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
        private string                $locale,
        private string                $timezone,
        ?DateFormatOptions            $options = null,
        ?DateFormatterCacheInterface  $cache = null,
        ?DateFormatterFactory         $dateFactory = null,
        ?RelativeDateFormatterFactory $relativeFormatterFactory = null
    ) {
        $this->cache = $cache ?? new DateFormatterCache();
        $this->dateFactory = $dateFactory ?? new DateFormatterFactory($options);
        $this->relativeFormatterFactory = $relativeFormatterFactory ?? new RelativeDateFormatterFactory();
    }

    /**
     * @param string $pattern format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $pattern): string
    {
        $options = new DateFormatOptions($pattern);
        $options->setLocale($this->locale);
        $options->setTimezone($this->timezone);

        $result = $this->getDateFormatter($options)->format(is_string($value) ? (int)strtotime($value) : $value);
        // @codeCoverageIgnoreStart
        if ($result === false) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new RuntimeException(sprintf('Unable to format date `%s` to format `%s`', $scalarValue, $pattern));
        }

        // @codeCoverageIgnoreEnd

        return $result;
    }

    public function formatRelative(
        int|string|DateTimeInterface $value,
        RelativeDateFormatOptions    $relativeOptions,
        DateFormatOptions            $fallback
    ): string {
        $result = $this->relativeFormatterFactory->create($this->locale)->format($this->getParsedDate($value));
        $defaultFormattedDate = $this->getDateFormatter(new DateFormatOptions('yyyy-MM-dd'))->format($this->getParsedDate($value));

        $currentDateTime = (new DateTimeImmutable())->setTime(0, 0);
        $resultDateTime = new DateTimeImmutable($this->validateResult($defaultFormattedDate, $value));

        if ($resultDateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
            return $this->validateResult($result, $value);
        }

        if ($relativeOptions->getRelativeDaysAmount() === 0
            || $resultDateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount()
        ) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
            return $this->validateResult($result, $value);
        }

        if ($defaultFormattedDate === $result) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
            return $this->validateResult($result, $value);
        }

        return $this->validateResult($result, $value);
    }

    private function getDateFormatter(DateFormatOptions $options): IntlDateFormatter
    {
        // get or create from cache
        return $this->cache->get((string)$options, fn() => $this->dateFactory->create($options));
    }

    private function getParsedDate(int|string|DateTimeInterface $date): int|DateTimeInterface
    {
        return is_string($date) ? (int)strtotime($date) : $date;
    }

    private function validateResult(bool|string|null $result, int|string|DateTimeInterface $value): string
    {
        // @codeCoverageIgnoreStart
        if (is_bool($result) || $result === null) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new RuntimeException(sprintf('Unable to format relative date %s', $scalarValue));
        }
        // @codeCoverageIgnoreEnd

        return $result;
    }
}
