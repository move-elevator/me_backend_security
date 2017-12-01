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

    public function setup()
    {
        $this->specialCharactersValidator =
            $this->getMockBuilder(SpecialCharactersValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $this->getExtensionConfigurationFixture()]])
                ->getMock();

        $this->specialCharactersValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');
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
