<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class ExtensionConfigurationFactoryTest extends TestCase
{
    use ExtensionConfigurationFixture;

    public function testCreateObjectFromValidArguments(): void
    {
        $rawExtensionConfiguration = $this->getRawExtensionConfigurationFixture();

        $extensionConfiguration = ExtensionConfigurationFactory::create($rawExtensionConfiguration);

        $this->assertInstanceOf(ExtensionConfiguration::class, $extensionConfiguration);
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

    public function testCreateObjectFromInvalidArguments(): void
    {
        $this->expectException(
            \InvalidArgumentException::class
        );

        ExtensionConfigurationFactory::create([]);
    }
}
