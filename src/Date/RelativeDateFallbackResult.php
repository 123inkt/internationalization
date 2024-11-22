<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

class RelativeDateFallbackResult
{
    public function __construct(private readonly bool $fallback, private readonly string|bool $date = '')
    {
    }

    public function shouldFallback(): bool
    {
        return $this->fallback;
    }

    public function getDate(): string|bool
    {
        return $this->date;
    }
}
