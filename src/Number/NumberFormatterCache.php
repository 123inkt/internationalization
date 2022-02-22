<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use NumberFormatter;

/**
 * @internal
 */
class NumberFormatterCache implements NumberFormatterCacheInterface
{
    /** @var NumberFormatter[] */
    private array $formatters = [];

    public function get(string $key, callable $factoryCallback): NumberFormatter
    {
        return $this->formatters[$key] ??= $factoryCallback();
    }
}
