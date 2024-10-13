<?php

declare(strict_types=1);

namespace DR\Internationalization\Time;

use DateTimeImmutable;
use DateTimeInterface;

class Time
{
    public function __construct(
        public readonly int $hour,
        public readonly int $minute,
        public readonly int $second,
        public readonly int $microsecond = 0
    ) {
    }

    public function addHours(int $hours): self
    {
        return new self(($this->hour + $hours) % 24, $this->minute, $this->second);
    }

    public function addMinutes(int $minutes): self
    {
        $hours   = ($this->hour + (int)floor(($this->minute + $minutes) / 60)) % 24;
        $minutes = ($this->minute + $minutes) % 60;

        return new self($hours, $minutes, $this->second);
    }

    public function addSeconds(int $seconds): self
    {
        $hours   = ($this->hour + (int)floor(($this->second + $seconds) / 3600)) % 24;
        $minutes = ($this->minute + (int)floor(($this->second + $seconds) / 60)) % 60;
        $seconds = ($this->second + $seconds) % 60;

        return new self($hours, $minutes, $seconds);
    }

    /**
     * See datetime.format for pattern.
     * @link https://www.php.net/manual/en/datetime.format.php
     */
    public function format(string $pattern): string
    {
        return $this->toDateTime()->format($pattern);
    }

    public function toDateTime(?DateTimeInterface $date = null): DateTimeImmutable
    {
        $date = $date === null
            ? new DateTimeImmutable()
            : (new DateTimeImmutable(timezone: $date->getTimezone()))->setTimestamp($date->getTimestamp());

        return $date->setTime($this->hour, $this->minute, $this->second, $this->microsecond);
    }

    public function __toString(): string
    {
        return sprintf('%02d:%02d:%02d', $this->hour, $this->minute, $this->second);
    }

    /**
     * @param string $time
     *
     * @return self
     */
    public static function fromString(string $time): self
    {
        $parts = explode(':', $time);

        return new self((int)$parts[0], (int)($parts[1] ?? 0), (int)($parts[2] ?? 0));
    }
}
