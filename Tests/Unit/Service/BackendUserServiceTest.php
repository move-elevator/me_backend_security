<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\ExtensionConfigurationFixture;
use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Saltedpasswords\Salt\Md5Salt;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class BackendUserServiceTest extends TestCase
{
    use ExtensionConfigurationFixture;

    protected $backendUserService;

    public function setup()
    {
        $backendUserAuthentication = $this->getMockBuilder(BackendUserAuthentication::class)
            ->disableOriginalConstructor()
            ->getMock();

        $backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = time();

        $databaseConnection = new DatabaseConnection();

        $extensionConfiguration = $this->getExtensionConfigurationFixture();

        $capitalCharactersValidator =
            $this->getMockBuilder(CapitalCharactersValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $extensionConfiguration]])
                ->getMock();

        $capitalCharactersValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');

        $compositeValidator =
            $this->getMockBuilder(CompositeValidator::class)
                ->setMethods(['translateErrorMessage'])
                ->setConstructorArgs([['extensionConfiguration' => $extensionConfiguration]])
                ->getMock();

        $compositeValidator
            ->method('translateErrorMessage')
            ->willReturn('translated message');

        $compositeValidator->append(
            $capitalCharactersValidator
        );

        $saltingInstance = new Md5Salt();

        $this->backendUserService = new BackendUserService(
            $backendUserAuthentication,
            $databaseConnection,
            $extensionConfiguration,
            $compositeValidator,
            $saltingInstance
        );
    }

    public function testCheckPasswordLifeTime()
    {
        $result = $this->backendUserService->checkPasswordLifeTime();

        $this->assertNull($result);
    }
}
