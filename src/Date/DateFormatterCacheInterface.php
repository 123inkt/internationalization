<?php

declare(strict_types=1);

namespace DR\Internationalization\Date;

use IntlDateFormatter;

/**
 * @internal
 */
interface DateFormatterCacheInterface
{
    /**
     * @param callable():IntlDateFormatter $factoryCallback
     */
    public function get(string $key, callable $factoryCallback): IntlDateFormatter;
}
