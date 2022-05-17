<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use Mockery;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\SamePasswordValidator;
use PHPUnit\Framework\TestCase;

class SamePasswordValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected SamePasswordValidator $samePasswordValidator;

    public function setUp(): void
    {
        $this->samePasswordValidator = Mockery::mock(
            SamePasswordValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $this->samePasswordValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');
    }

    public function testIgnoredValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('bar');
        $passwordChangeRequest->setPasswordConfirmation('bar');

        $result = $this->samePasswordValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testPositiveValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setCurrentPassword('foo');
        $passwordChangeRequest->setPassword('bar');
        $passwordChangeRequest->setPasswordConfirmation('bar');

        $result = $this->samePasswordValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setCurrentPassword('foo');
        $passwordChangeRequest->setPassword('foo');
        $passwordChangeRequest->setPasswordConfirmation('foo');

        $result = $this->samePasswordValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
