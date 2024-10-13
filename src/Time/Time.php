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

    public function subtractHours(int $hours): self
    {
        $hours = ($this->hour - $hours) % 24;
        $hours = $hours < 0 ? 24 + $hours : $hours;

        return new self($hours, $this->minute, $this->second);
    }

    public function addMinutes(int $minutes): self
    {
        $minutes = ($this->hour * 60 + $this->minute + $minutes) % 1440;

        return new self((int)floor($minutes / 60), $minutes % 60, $this->second);
    }

    public function subtractMinutes(int $minutes): self
    {
        $minutes = ($this->hour * 60 + $this->minute - $minutes) % 1440;
        $minutes = $minutes < 0 ? 1440 + $minutes : $minutes;

        return new self((int)floor($minutes / 60), $minutes % 60, $this->second);
    }

    public function addSeconds(int $seconds): self
    {
        $seconds = (($this->hour * 3600 + $this->minute * 60 + $this->second) + $seconds) % 86400;

        return self::fromSeconds($seconds);
    }

    public function subtractSeconds(int $seconds): self
    {
        $seconds = (($this->hour * 3600 + $this->minute * 60 + $this->second) - $seconds) % 86400;
        $seconds = $seconds < 0 ? 86400 + $seconds : $seconds;

        return self::fromSeconds($seconds);
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

    public static function fromSeconds(int $seconds): self
    {
        $hours   = (int)floor($seconds / 3600);
        $minutes = (int)floor(($seconds % 3600) / 60);
        $seconds %= 60;

        return new self($hours, $minutes, $seconds);
    }
}
