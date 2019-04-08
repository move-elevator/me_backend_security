<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordConfirmationValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class PasswordConfirmationValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    /**
     * @var PasswordConfirmationValidator
     */
    protected $passwordConfirmationValidator;

    public function setUp(): void
    {
        $this->passwordConfirmationValidator = \Mockery::mock(
            PasswordConfirmationValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $this->passwordConfirmationValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');
    }

    public function testPositiveValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('foo');
        $passwordChangeRequest->setPasswordConfirmation('foo');

        $result = $this->passwordConfirmationValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('foo');
        $passwordChangeRequest->setPasswordConfirmation('bar');

        $result = $this->passwordConfirmationValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
