<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\TypoScriptSetupFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\DigitsValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\LowercaseCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordConfirmationValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordLengthValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\SpecialCharactersValidator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class CompositeValidatorFactoryTest extends TestCase
{
    use ExtensionConfigurationFixture;
    use TypoScriptSetupFixture;

    protected $objectManager;

    public function setup()
    {
        $extensionConfiguration = $this->getExtensionConfigurationFixture();

        $this->objectManager = \Mockery::mock(ObjectManager::class);
        $this->objectManager
            ->shouldReceive('get')
            ->andReturn(
                new CompositeValidator(['extensionConfiguration' => $extensionConfiguration]),
                new CapitalCharactersValidator(['extensionConfiguration' => $extensionConfiguration]),
                new DigitsValidator(['extensionConfiguration' => $extensionConfiguration]),
                new LowercaseCharactersValidator(['extensionConfiguration' => $extensionConfiguration]),
                new PasswordConfirmationValidator(['extensionConfiguration' => $extensionConfiguration]),
                new PasswordLengthValidator(['extensionConfiguration' => $extensionConfiguration]),
                new SpecialCharactersValidator(['extensionConfiguration' => $extensionConfiguration])
            );
    }

    public function testCreateObjectFromValidArguments()
    {
        $extensionConfiguration = $this->getExtensionConfigurationFixture();
        $rawTypoScriptSetupFixture = $this->getRawTypoScriptSetupFixture();

        $compositeValidator = CompositeValidatorFactory::create(
            $this->objectManager,
            $extensionConfiguration,
            $rawTypoScriptSetupFixture
        );

        $this->assertInstanceOf(CompositeValidator::class, $compositeValidator);
    }

    public function testCreateObjectFromInvalidArguments()
    {
        $extensionConfiguration = $this->getExtensionConfigurationFixture();

        $this->expectException(\InvalidArgumentException::class);

        CompositeValidatorFactory::create(
            $this->objectManager,
            $extensionConfiguration,
            ['invalid value']
        );
    }
}
