<?php

declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\Date;

use DateTimeImmutable;
use DR\Internationalization\Date\DateFormatHelper;
use DR\Internationalization\Date\DateFormatOptions;
use DR\Internationalization\Date\DateFormatterCacheInterface;
use DR\Internationalization\Date\DateFormatterFactory;
use IntlDateFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(DateFormatHelper::class)]
class DateFormatHelperTest extends TestCase
{
    private DateFormatterCacheInterface&MockObject $cache;
    private DateFormatterFactory&MockObject $dateFormatterFactory;
    private DateFormatHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->createMock(DateFormatterCacheInterface::class);
        $this->dateFormatterFactory = $this->createMock(DateFormatterFactory::class);

        $this->helper = new DateFormatHelper($this->cache, $this->dateFormatterFactory);
    }

    public function testGetDateFormatter(): void
    {
        $options = new DateFormatOptions('nl', 'Europe/Amsterdam');
        $dateFormatter = $this->createMock(IntlDateFormatter::class);

        $this->cache->expects(self::once())->method('get')->with($options . 'Y-m-d')->willReturn($dateFormatter);

        $result = $this->helper->getDateFormatter($options, 'Y-m-d');

        static::assertSame($dateFormatter, $result);
    }

    public function testGetParsedDateInt(): void
    {
        $timestamp = 1732369685;

        $expectedResult = DateTimeImmutable::createFromFormat('U', (string)$timestamp);
        $result = $this->helper->getParsedDate($timestamp);

        static::assertSame($expectedResult->getTimestamp(), $result->getTimestamp());
    }
}
