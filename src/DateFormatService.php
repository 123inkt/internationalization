<?php
declare(strict_types=1);

namespace DR\Internationalization;

use DateTimeInterface;
use DR\Internationalization\Date\DateFormatHelper;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\RelativeDateFallbackService;
use DR\Internationalization\Date\RelativeDateFormatOptions;
use DR\Internationalization\Date\RelativeDateFormatterFactory;

class DateFormatService
{
    private DateFormatHelper $dateFormatHelper;
    private RelativeDateFormatterFactory $relativeFormatterFactory;
    private RelativeDateFallbackService $fallbackHandler;

    public function __construct(
        private readonly DateFormatOptions $options,
        ?DateFormatHelper                  $formatHelper = null,
        ?RelativeDateFormatterFactory      $relativeFormatterFactory = null,
        ?RelativeDateFallbackService       $fallbackHandler = null
    ) {
        $this->dateFormatHelper = $formatHelper ?? new DateFormatHelper();
        $this->relativeFormatterFactory = $relativeFormatterFactory ?? new RelativeDateFormatterFactory();
        $this->fallbackHandler = $fallbackHandler ?? new RelativeDateFallbackService();
    }

    /**
     * @param string $pattern format according to ICU
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     */
    public function format(int|string|DateTimeInterface $value, string $pattern, ?DateFormatOptions $options = null): string
    {
        $options = $options ?? $this->options;
        $result = $this->dateFormatHelper->getDateFormatter($options, $pattern)->format(is_string($value) ? (int)strtotime($value) : $value);

        return $this->dateFormatHelper->validateResult($result, $value, $pattern);
    }

    public function formatRelative(
        int|string|DateTimeInterface $value,
        string                       $pattern,
        RelativeDateFormatOptions    $relativeOptions,
        DateFormatOptions            $fallbackOptions
    ): string {
        $parsedValue = $this->dateFormatHelper->getParsedDate($value);

        if ($this->fallbackHandler->shouldFallback($parsedValue, $relativeOptions)) {
            $result = $this->dateFormatHelper->getDateFormatter($fallbackOptions, $pattern)->format($parsedValue);
            return $this->dateFormatHelper->validateResult($result, $value, $pattern);
        }

        $relativeFullDate = $this->relativeFormatterFactory->createRelativeFull($this->options->getLocale())->format($parsedValue);
        $fullDate = $this->relativeFormatterFactory->createFull($this->options->getLocale())->format($parsedValue);

        if ($relativeFullDate === $fullDate) {
            $result = $this->dateFormatHelper->getDateFormatter($fallbackOptions, $pattern)->format($parsedValue);
            return $this->dateFormatHelper->validateResult($result, $value, $pattern);
        }

        return $this->dateFormatHelper->validateResult($relativeFullDate, $value, $pattern);
    }
}
