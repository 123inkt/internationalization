<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use DateTimeImmutable;
use DateTimeInterface;
use IntlDateFormatter;
use RuntimeException;

class DateFormatHelper
{
    private DateFormatterCacheInterface $cache;
    private DateFormatterFactory $dateFactory;

    public function __construct(?DateFormatterCacheInterface $cache = null, ?DateFormatterFactory $dateFactory = null)
    {
        $this->cache = $cache ?? new DateFormatterCache();
        $this->dateFactory = $dateFactory ?? new DateFormatterFactory();
    }

    public function getDateFormatter(DateFormatOptions $options, string $pattern): IntlDateFormatter
    {
        // Get or create from cache.
        return $this->cache->get($options . $pattern, fn() => $this->dateFactory->create($options, $pattern));
    }

    public function getParsedDate(int|string|DateTimeInterface $date): DateTimeInterface
    {
        if (is_string($date)) {
            return new DateTimeImmutable($date);
        }

        if (is_int($date)) {
            return new DateTimeImmutable('@' . $date);
        }

        return $date;
    }

    public function validateResult(bool|string|null $result, int|string|DateTimeInterface $value, string $pattern): string
    {
        // @codeCoverageIgnoreStart
        if (is_bool($result) || $result === null) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new RuntimeException(sprintf('Unable to format date `%s` to format `%s`', $scalarValue, $pattern));
        }
        // @codeCoverageIgnoreEnd

        return $result;
    }
}
