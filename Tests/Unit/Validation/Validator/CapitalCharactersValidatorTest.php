<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class CapitalCharactersValidatorTest extends TestCase
{
    use ExtensionConfigurationFixture;

    /**
     * @var CapitalCharactersValidator
     */
    protected $capitalCharactersValidator;

    public function setUp(): void
    {
        $this->capitalCharactersValidator = \Mockery::mock(
            CapitalCharactersValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->getExtensionConfigurationFixture()]]
        );
        $this->capitalCharactersValidator
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');
    }

    public function testPositiveValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');

        $result = $this->capitalCharactersValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 0);
    }

    public function testNegativeValidation(): void
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('a');

        $result = $this->capitalCharactersValidator->validate($passwordChangeRequest);

        $this->assertEquals(count($result->getErrors()), 1);
    }
}
