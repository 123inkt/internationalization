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
    private RelativeDateFormatOptions $relativeDateFormatOptions;
    private DateFormatOptions $options;


    public function __construct(
        DateFormatOptions|string     $options,
        DateFormatHelper|string|null $formatHelper = null,
        ?RelativeDateFallbackService $fallbackHandler = null,
        ?RelativeDateFormatOptions   $relativeDateFormatOptions = null
    ) {
        if (is_string($options) && is_string($formatHelper)) {
            $this->options = new DateFormatOptions($options, $formatHelper);
        }
        if ($options instanceof DateFormatOptions) {
            $this->options = $options;
        }
        if ($formatHelper instanceof DateFormatHelper) {
            $this->dateFormatHelper = $formatHelper;
        }
        if ($formatHelper === null || is_string($formatHelper)) {
            $this->dateFormatHelper = new DateFormatHelper();
        }

        $this->fallbackHandler = $fallbackHandler ?? new RelativeDateFallbackService();
        $this->relativeDateFormatOptions = $relativeDateFormatOptions ?? new RelativeDateFormatOptions(null);
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
        ?RelativeDateFormatOptions   $relativeOptions = null,
        ?DateFormatOptions           $options = null
    ): string {
        $parsedValue = $this->dateFormatHelper->getParsedDate($value);
        $options ??= $this->options;
        $relativeOptions ??= $this->relativeDateFormatOptions;

        $fallbackResult = $this->fallbackHandler->getFallbackResult($options->getLocale(), $parsedValue, $relativeOptions);

        if ($fallbackResult->isFallback()) {
            $result = $this->dateFormatHelper->getDateFormatter($options, $pattern)->format($parsedValue);
            return $this->dateFormatHelper->validateResult($result, $value, $pattern);
        }

        return $fallbackResult->getDate();
    }
}
