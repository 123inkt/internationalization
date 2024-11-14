<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\DateFormatterCache;
use DR\Internationalization\Date\DateFormatterCacheInterface;
use DR\Internationalization\Date\DateFormatterFactory;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use DR\Internationalization\Date\RelativeDateFormatterFactory;
use IntlDateFormatter;

class DateFormatService
{
    private const MAX_TRANSLATABLE_DAYS_AMOUNT = 4;

    public function __construct(
        private string                        $locale,
        private string                        $timezone,
        private ?DateFormatOptions            $options = null,
        private ?DateFormatterCacheInterface  $cache = null,
        private ?DateFormatterFactory         $dateFactory = null,
        private ?RelativeDateFormatterFactory $relativeFormatterFactory = null
    )
    {
        $this->cache ??= new DateFormatterCache();
        $this->dateFactory ??= new DateFormatterFactory($this->options);
        $this->relativeFormatterFactory ??= new RelativeDateFormatterFactory();
    }

    /**
     * @param string $format format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $pattern): string
    {
        $result = $this->getDateFormatter(new DateFormatOptions($pattern))->format(is_string($value) ? (int)strtotime($value) : $value);
        // @codeCoverageIgnoreStart
        if ($result === false) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new \RuntimeException(sprintf('Unable to format date `%s` to format `%s`', $scalarValue, $pattern));
        }

        // @codeCoverageIgnoreEnd

        return $result;
    }

    public function formatRelative(int|string|DateTimeInterface $value, RelativeDateFormatOptions $relativeOptions, DateFormatOptions $fallback): string
    {
        $result = $this->relativeFormatterFactory->create($this->locale)->format($this->getParsedDate($value));
        $defaultFormattedDate = $this->getDateFormatter(new DateFormatOptions('yyyy-MM-dd'))->format($this->getParsedDate($value));

        $currentDateTime = (new DateTimeImmutable())->setTime(0,0);
        $resultDateTime = new DateTimeImmutable($defaultFormattedDate);

        if ($resultDateTime->diff($currentDateTime)->d > self::MAX_TRANSLATABLE_DAYS_AMOUNT) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
        } elseif ($resultDateTime->diff($currentDateTime)->d > $relativeOptions->getRelativeDaysAmount() || $relativeOptions->getRelativeDaysAmount() === 0) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
        } elseif ($defaultFormattedDate === $result) {
            $result = $this->getDateFormatter($fallback)->format($this->getParsedDate($value));
        }

        if ($result === false) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new \RuntimeException(sprintf('Unable to format relative date %s', $scalarValue));
        }

        return $result;
    }

    private function getDateFormatter(DateFormatOptions $options): IntlDateFormatter
    {
        // get or create from cache
        return $this->cache->get((string)$options, fn() => $this->dateFactory->create($options));
    }

    private function getParsedDate(int|string|DateTimeInterface $date): int|DateTimeInterface
    {
        return is_string($date) ? (int)strtotime($date) : $date->getTimestamp();
    }
}
