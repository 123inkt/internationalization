<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\DateFormatOptionsHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatOptionsHelper::class)]
class DateFormatOptionsHelperTest extends TestCase
{
    public function testOptions(): void
    {
        $formatOptions = new DateFormatOptions('Y-m-d');
        $optionsHelper = new DateFormatOptionsHelper($formatOptions);

        $optionsHelper->setOptions($formatOptions);
        self::assertNotSame($formatOptions, $optionsHelper->getOptions());
    }
}
