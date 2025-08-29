<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\PhoneNumber;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\PhoneNumber\PhoneNumberFormatEnum;
use DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhoneNumberFormatOptions::class)]
class PhoneNumberFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setExcludedMethods(['setFormat', 'getFormat']);
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        static::assertAccessorPairs(PhoneNumberFormatOptions::class, $config);
    }

    public function testFormat(): void
    {
        $options = new PhoneNumberFormatOptions();
        $options->setFormat(PhoneNumberFormatEnum::INTERNATIONAL_DIAL);
        static::assertSame(PhoneNumberFormatEnum::INTERNATIONAL_DIAL, $options->getFormat());
    }
}
