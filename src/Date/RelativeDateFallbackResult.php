<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

/**
 * @internal
 */
class RelativeDateFallbackResult
{
    public function __construct(private readonly bool $fallback, private readonly string $date = '')
    {
    }

    public function isFallback(): bool
    {
        return $this->fallback;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
