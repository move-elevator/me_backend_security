<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class ExtensionConfigurationTest extends TestCase
{
    use ExtensionConfigurationFixture;

    public function testNoManipulationInSetterAndGetter()
    {
        $extensionConfiguration = $this->getAddressFixtureObject();

        $this->assertEquals($this->specialChar, $extensionConfiguration->getSpecialChar());
        $this->assertEquals($this->digit, $extensionConfiguration->getDigit());
        $this->assertEquals($this->capitalChar, $extensionConfiguration->getCapitalChar());
        $this->assertEquals($this->lowercaseChar, $extensionConfiguration->getLowercaseChar());
        $this->assertEquals($this->passwordLength, $extensionConfiguration->getPasswordLength());
        $this->assertEquals($this->validUntil, $extensionConfiguration->getValidUntil());
    }
}
