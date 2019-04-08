<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class CompositeValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    /**
     * @var CapitalCharactersValidator
     */
    protected $capitalCharactersValidator;

    /**
     * @var CompositeValidator
     */
    protected $compositeValidator;

    public function setUp(): void
    {
        $this->compositeValidator = \Mockery::mock(
            CompositeValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $this->compositeValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');

        $capitalCharactersValidator = \Mockery::mock(
            CapitalCharactersValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $capitalCharactersValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');

        $this->compositeValidator->append(
            $capitalCharactersValidator
        );
    }

    public function testPositiveValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');

        $result = $this->compositeValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('a');

        $result = $this->compositeValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }

    public function testValidationWithInvalidArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->compositeValidator->validate('anything');
    }
}
