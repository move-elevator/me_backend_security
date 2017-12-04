<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\TypoScriptSetupFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
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
        $this->objectManager = \Mockery::mock(ObjectManager::class);
        $this->objectManager
            ->shouldReceive('get')
            ->andReturnUsing(function($class, $options) { return new CompositeValidator($options); });
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
