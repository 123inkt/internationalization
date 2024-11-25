<?php
declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

/**
 * @internal
 */
class DateFormatterCache implements DateFormatterCacheInterface
{
    /** @var IntlDateFormatter[] */
    private array $formatters = [];

    public function get(string $key, callable $factoryCallback): IntlDateFormatter
    {
        return $this->formatters[$key] ??= $factoryCallback();
    }
}
