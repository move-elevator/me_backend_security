<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\SpecialCharactersValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class SpecialCharactersValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $specialCharactersValidator;

    public function setUp()
    {
        $this->specialCharactersValidator = \Mockery::mock(
            SpecialCharactersValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $this->specialCharactersValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');
    }

    public function testPositiveValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('%');

        $result = $this->specialCharactersValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');

        $result = $this->specialCharactersValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
