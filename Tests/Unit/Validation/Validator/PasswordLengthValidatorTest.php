<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordLengthValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class PasswordLengthValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $passwordLengthValidator;

    public function setup()
    {
        $this->passwordLengthValidator =
            $this->getMockBuilder(PasswordLengthValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $this->getExtensionConfigurationFixture()]])
                ->getMock();

        $this->passwordLengthValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');
    }

    public function testPositiveValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('12345678');

        $result = $this->passwordLengthValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('foo');

        $result = $this->passwordLengthValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
