<?php
declare(strict_types=1);

namespace DR\Internationalization\Tests\Unit\PhoneNumber;

use DigitalRevolution\AccessorPairConstraint\AccessorPairAsserter;
use DigitalRevolution\AccessorPairConstraint\Constraint\ConstraintConfig;
use DR\Internationalization\PhoneNumber\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DR\Internationalization\PhoneNumber\PhoneNumber
 */
class PhoneNumberTest extends TestCase
{
    use AccessorPairAsserter;

    /**
     * @covers ::getInternationalDialCode
     * @covers ::getCountryDialCode
     * @covers ::getNationalNumber
     * @covers ::getExtension
     * @covers ::getRawInput
     * @covers ::getCountryCode
     * @covers ::getNumberType
     * @covers ::getPhoneNumber
     */
    public function testAccessors(): void
    {
        $config = new ConstraintConfig();
        $config->setAssertPropertyDefaults(true);
        $config->setAssertConstructor(false);
        $config->setAssertAccessorPair(true);


        static::assertAccessorPairs(PhoneNumber::class, $config);
    }
}
