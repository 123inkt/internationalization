<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\PhoneNumber;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\PhoneNumber\PhoneNumberFormatOptions
 */
class PhoneNumberFormatOptionsTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::getDefaultRegion
     * @covers ::setDefaultRegion
     * @covers ::getFormat
     * @covers ::setFormat
     */
    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(true);
        $config->setAssertAccessorPair(true);
        static::assertAccessorPairs(PhoneNumberFormatOptions::class, $config);
    }
}
