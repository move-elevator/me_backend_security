<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Service;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\Argon2iPasswordHash;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class BackendUserServiceTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $backendUserAuthentication;
    protected $queryBuilder;
    protected $expressionBuilder;
    protected $extensionConfiguration;
    protected $compositeValidator;
    protected $passwordHashInstance;

    public function setUp(): void
    {
        $this->backendUserAuthentication = \Mockery::mock(BackendUserAuthentication::class);

        $this->expressionBuilder = \Mockery::mock(ExpressionBuilder::class);
        $this->expressionBuilder
            ->shouldReceive('eq')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->expressionBuilder;
            });

        $this->queryBuilder = \Mockery::mock(QueryBuilder::class);
        $this->queryBuilder
            ->shouldReceive('count', 'from', 'update', 'where', 'set', 'setParameter', 'execute')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->queryBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('expr')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->expressionBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('fetchOne')
            ->withAnyArgs()
            ->andReturn(1);

        $this->extensionConfiguration = $this->getExtensionConfigurationFixture();

        $capitalCharactersValidator = \Mockery::mock(
            CapitalCharactersValidator::class . '[translateErrorMessage]',
            [['extensionConfiguration' => $this->extensionConfiguration]]
        )->shouldAllowMockingProtectedMethods();
        $capitalCharactersValidator
            ->shouldReceive('translateErrorMessage')
            ->withAnyArgs()
            ->andReturn('translated message');

        $this->compositeValidator = \Mockery::mock(
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

        $this->passwordHashInstance = \Mockery::mock(Argon2iPasswordHash::class);
        $this->passwordHashInstance
            ->shouldReceive('getHashedPassword')
            ->withAnyArgs()
            ->andReturnUsing(function ($argument) {
                return $argument;
            });
    }

    public function testCheckPasswordIsValid(): void
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = time();
        $this->backendUserAuthentication->user['lastlogin'] = time();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->passwordHashInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithValidationError(): void
    {
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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

        $this->queryBuilder = \Mockery::mock(QueryBuilder::class);
        $this->queryBuilder
            ->shouldReceive('count', 'from', 'update', 'where', 'set', 'setParameter', 'execute')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->queryBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('expr')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->expressionBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('fetchOne')
            ->withAnyArgs()
            ->andReturn(false);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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

        $this->queryBuilder = \Mockery::mock(QueryBuilder::class);
        $this->queryBuilder
            ->shouldReceive('count', 'from', 'update', 'where', 'set', 'setParameter', 'execute')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->queryBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('expr')
            ->withAnyArgs()
            ->andReturnUsing(function () {
                return $this->expressionBuilder;
            });
        $this->queryBuilder
            ->shouldReceive('fetchOne')
            ->withAnyArgs()
            ->andReturn(1);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->queryBuilder,
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
