<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

class RelativeDateFormatOptions
{
    public function __construct(private readonly ?int $relativeDaysAmount)
    {
    }

    public function getRelativeDaysAmount(): ?int
    {
        return $this->relativeDaysAmount;
    }
}
