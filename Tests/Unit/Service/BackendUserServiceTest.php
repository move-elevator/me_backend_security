<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Service;

use Mockery;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Domain\Repository\BackendUserRepository;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\Argon2iPasswordHash;
use TYPO3\CMS\Extbase\Error\Result;

class BackendUserServiceTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $backendUserAuthentication;
    protected $backendUserRepository;
    protected $expressionBuilder;
    protected $extensionConfiguration;
    protected $compositeValidator;
    protected $passwordHashInstance;

    public function setUp(): void
    {
        $this->backendUserAuthentication = Mockery::mock(BackendUserAuthentication::class);
        $this->backendUserRepository = Mockery::mock(BackendUserRepository::class);
        $this->extensionConfiguration = $this->getExtensionConfigurationFixture();

        $capitalCharactersValidator = Mockery::mock(
            CapitalCharactersValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->extensionConfiguration]]
        )->shouldAllowMockingProtectedMethods();
        $capitalCharactersValidator
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');

        $this->compositeValidator = Mockery::mock(
            CompositeValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->extensionConfiguration]]
        )->shouldAllowMockingProtectedMethods();
        $this->compositeValidator
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');

        $this->compositeValidator->append(
            $capitalCharactersValidator
        );

        $this->passwordHashInstance = Mockery::mock(Argon2iPasswordHash::class);
        $this->passwordHashInstance
            ->shouldReceive('getHashedPassword')
            ->withAnyArgs()
            ->andReturnUsing(function ($argument) {
                return $argument;
            });
    }

    public function testCheckPasswordIsValid(): void
    {
        $this->backendUserAuthentication->user['uid'] = 1;
        $this->backendUserAuthentication->user['username'] = 'testuser';
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = time();
        $this->backendUserAuthentication->user['lastlogin'] = time();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertNull($result);
    }

    public function testCheckPasswordIsNonMigrated(): void
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = 0;
        $this->backendUserAuthentication->user['lastlogin'] = time();
        $this->backendUserAuthentication->user['uid'] = 1;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $this->backendUserRepository
            ->shouldReceive('migrate')
            ->withAnyArgs();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertNull($result);
    }

    public function testCheckPasswordIsNeverChanged(): void
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = 0;
        $this->backendUserAuthentication->user['lastlogin'] = 0;
        $this->backendUserAuthentication->user['uid'] = 1;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $this->backendUserRepository
            ->shouldReceive('updateLastChangeAndLogin')
            ->withAnyArgs();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testCheckPasswordIsInvalid(): void
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = 631152000;
        $this->backendUserAuthentication->user['lastlogin'] = time();
        $this->backendUserAuthentication->user['uid'] = 1;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $result = $this->compositeValidator
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn(Result::class);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithValidationError(): void
    {
        $this->backendUserAuthentication->user['uid'] = 1;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('foo');
        $passwordChangeRequest->setPasswordConfirmation('bar');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithNotExistingUser(): void
    {
        $this->backendUserAuthentication->user['uid'] = '1';
        $this->backendUserAuthentication->user['username'] = 'nobody';

        $this->backendUserRepository
            ->shouldReceive('isUserPresent')
            ->withAnyArgs()
            ->andReturn(false);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');
        $passwordChangeRequest->setPasswordConfirmation('A');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithExistingUser(): void
    {
        $this->backendUserAuthentication->user['uid'] = '1';
        $this->backendUserAuthentication->user['username'] = 'test';

        $this->backendUserRepository
            ->shouldReceive('isUserPresent')
            ->withAnyArgs()
            ->andReturn(true);

        $this->backendUserRepository
            ->shouldReceive('updatePassword')
            ->withAnyArgs();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');
        $passwordChangeRequest->setPasswordConfirmation('A');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertNull($result);
    }
}
