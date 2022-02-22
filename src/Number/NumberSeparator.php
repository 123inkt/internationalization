<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

class NumberSeparator
{
    private ?string $thousand;
    private ?string $decimal;

    public function __construct(?string $decimal, ?string $thousand)
    {
        $this->decimal  = $decimal;
        $this->thousand = $thousand;
    }

    public function getDecimal(): ?string
    {
        return $this->decimal;
    }

    public function getThousand(): ?string
    {
        return $this->thousand;
    }
}
