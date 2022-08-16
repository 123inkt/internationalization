<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DateTimeInterface;
use IntlDateFormatter;

class DateFormatService
{
    private array $formatters = [];

    public function __construct(private string $locale, private string $timezone)
    {
    }

    /**
     * @param string $format format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $format): string
    {
        $result = $this->getDateFormatter($format)->format(is_string($value) ? (int)strtotime($value) : $value);
        // @codeCoverageIgnoreStart
        if ($result === false) {
            $scalarValue = $value instanceof DateTimeInterface ? $value->getTimestamp() : $value;
            throw new \RuntimeException(sprintf('Unable to format date `%s` to format `%s`', (string)$scalarValue, $format));
        }

        // @codeCoverageIgnoreEnd

        return $result;
    }

    private function getDateFormatter(string $format): IntlDateFormatter
    {
        if (isset($this->formatters[$format])) {
            return $this->formatters[$format];
        }

        return $this->formatters[$format] = new IntlDateFormatter(
            $this->locale,
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            $this->timezone,
            IntlDateFormatter::GREGORIAN,
            $format
        );
    }
}
