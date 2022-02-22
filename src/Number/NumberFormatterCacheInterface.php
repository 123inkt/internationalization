<?php
declare(strict_types=1);

namespace DR\Internationalization\Number;

use NumberFormatter;

interface NumberFormatterCacheInterface
{
    /**
     * @param callable():NumberFormatter $factoryCallback
     */
    public function get(string $key, callable $factoryCallback): NumberFormatter;
}
