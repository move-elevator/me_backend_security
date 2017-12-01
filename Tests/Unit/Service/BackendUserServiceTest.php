<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
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

    public function setup()
    {
        $this->backendUserAuthentication = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->databaseConnection = new DatabaseConnection();

        $this->extensionConfiguration = $this->getExtensionConfigurationFixture();

        $capitalCharactersValidator =
            $this->getMockBuilder(CapitalCharactersValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $this->extensionConfiguration]])
                ->getMock();

        $capitalCharactersValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');

        $this->compositeValidator =
            $this->getMockBuilder(CompositeValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $this->extensionConfiguration]])
                ->getMock();

        $this->compositeValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');

        $this->compositeValidator->append(
            $capitalCharactersValidator
        );

        $this->saltingInstance = new Md5Salt();
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

    public function testCheckPasswordIsInvalid()
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
}
