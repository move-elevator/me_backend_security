<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\DigitsValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class DigitsValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $digitsValidator;

    public function setup()
    {
        $this->digitsValidator =
            $this->getMockBuilder(DigitsValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $this->getExtensionConfigurationFixture()]])
                ->getMock();

        $this->digitsValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');
    }

    public function testPositiveValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('1');

        $result = $this->digitsValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('a');

        $result = $this->digitsValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
