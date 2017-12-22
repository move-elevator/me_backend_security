<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Saltedpasswords\Salt\Md5Salt;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class BackendUserServiceTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $backendUserAuthentication;
    protected $databaseConnection;
    protected $extensionConfiguration;
    protected $compositeValidator;
    protected $saltingInstance;

    public function setUp()
    {
        $this->backendUserAuthentication = \Mockery::mock(BackendUserAuthentication::class);

        $this->databaseConnection = \Mockery::mock(DatabaseConnection::class);
        $this->databaseConnection
            ->shouldReceive('fullQuoteStr')
            ->withAnyArgs()
            ->andReturnUsing(function($argument) { return $argument; });
        $this->databaseConnection
            ->shouldReceive('exec_UPDATEquery')
            ->withAnyArgs()
            ->andReturn(true);

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

        $this->saltingInstance = \Mockery::mock(Md5Salt::class);
        $this->saltingInstance
            ->shouldReceive('getHashedPassword')
            ->withAnyArgs()
            ->andReturnUsing(function($argument) { return $argument; });
    }

    public function testCheckPasswordIsValid()
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = time();

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertNull($result);
    }

    public function testCheckPasswordIsNeverChanged()
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = 0;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testCheckPasswordIsInvalid()
    {
        $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = 631152000;
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $result = $backendUserService->checkPasswordLifeTime();

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithValidationError()
    {
        $this->backendUserAuthentication->user['username'] = 'testuser';

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('foo');
        $passwordChangeRequest->setPasswordConfirmation('bar');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithNotExistingUser()
    {
        $this->backendUserAuthentication->user['uid'] = '1';
        $this->backendUserAuthentication->user['username'] = 'nobody';

        $this->databaseConnection
            ->shouldReceive('exec_SELECTcountRows')
            ->withArgs(['uid', 'be_users', 'uid=1 AND username=nobody'])
            ->andReturn(false);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');
        $passwordChangeRequest->setPasswordConfirmation('A');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertInstanceOf(LoginProviderRedirect::class, $result);
    }

    public function testHandlePasswordChangeRequestWithExistingUser()
    {
        $this->backendUserAuthentication->user['uid'] = '1';
        $this->backendUserAuthentication->user['username'] = 'test';

        $this->databaseConnection
            ->shouldReceive('exec_SELECTcountRows')
            ->withArgs(['uid', 'be_users', 'uid=1 AND username=test'])
            ->andReturn(1);

        $backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->databaseConnection,
            $this->extensionConfiguration,
            $this->compositeValidator,
            $this->saltingInstance
        );

        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword('A');
        $passwordChangeRequest->setPasswordConfirmation('A');

        $result = $backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        $this->assertNull($result);
    }
}
