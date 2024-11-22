<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DateTimeInterface;
use DR\Internationalization\Date\DateFormatHelper;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\RelativeDateFallbackService;
use DR\Internationalization\Date\RelativeDateFormatOptions;

class DateFormatService
{
    private DateFormatHelper $dateFormatHelper;
    private RelativeDateFallbackService $fallbackHandler;

    public function __construct(
        private readonly DateFormatOptions $options,
        ?DateFormatHelper                  $formatHelper = null,
        ?RelativeDateFallbackService       $fallbackHandler = null
    ) {
        $this->dateFormatHelper = $formatHelper ?? new DateFormatHelper();
        $this->fallbackHandler = $fallbackHandler ?? new RelativeDateFallbackService($this->options->getLocale());
    }

    /**
     * @param string $pattern format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $pattern, ?DateFormatOptions $options = null): string
    {
        $parsedValue = $this->dateFormatHelper->getParsedDate($value);
        $options = $options ?? $this->options;
        $result = $this->dateFormatHelper->getDateFormatter($options, $pattern)->format($parsedValue);

        return $this->dateFormatHelper->validateResult($result, $value, $pattern);
    }

    public function formatRelative(
        int|string|DateTimeInterface $value,
        string                       $pattern,
        RelativeDateFormatOptions    $relativeOptions
    ): string {
        $parsedValue = $this->dateFormatHelper->getParsedDate($value);
        $fallbackResult = $this->fallbackHandler->getFallbackResult($parsedValue, $relativeOptions);

        if ($fallbackResult->shouldFallback()) {
            $result = $this->dateFormatHelper->getDateFormatter($this->options, $pattern)->format($parsedValue);
            return $this->dateFormatHelper->validateResult($result, $value, $pattern);
        }

        return $this->dateFormatHelper->validateResult($fallbackResult->getDate(), $value, $pattern);
    }
}
