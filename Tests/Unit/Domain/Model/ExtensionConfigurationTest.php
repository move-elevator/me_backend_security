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
        $extensionConfiguration = $this->getExtensionConfigurationFixture();

        $this->assertEquals($this->minimumSpecialCharacters, $extensionConfiguration->getMinimumSpecialCharacters());
        $this->assertEquals($this->minimumDigits, $extensionConfiguration->getMinimumDigits());
        $this->assertEquals($this->minimumCapitalCharacters, $extensionConfiguration->getMinimumCapitalCharacters());
        $this->assertEquals(
            $this->minimumLowercaseCharacters,
            $extensionConfiguration->getMinimumLowercaseCharacters()
        );
        $this->assertEquals($this->passwordLength, $extensionConfiguration->getPasswordLength());
        $this->assertEquals($this->maximumValidDays, $extensionConfiguration->getMaximumValidDays());
    }
}
