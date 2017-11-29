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
        $extensionConfiguration = $this->getFixtureObject();

        $this->assertEquals($this->minimumSpecialCharacters, $extensionConfiguration->getMinimalSpecialCharacters());
        $this->assertEquals($this->minimumDigits, $extensionConfiguration->getMinimalDigits());
        $this->assertEquals($this->minimumCapitalCharacters, $extensionConfiguration->getMinimalCapitalCharacters());
        $this->assertEquals(
            $this->minimumLowercaseCharacters,
            $extensionConfiguration->getMinimalLowercaseCharacters()
        );
        $this->assertEquals($this->passwordLength, $extensionConfiguration->getPasswordLength());
        $this->assertEquals($this->maximumValidDays, $extensionConfiguration->getMaximumValidDays());
    }
}
