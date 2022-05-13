<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Authentication;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\SysLog\Action\Login as SystemLogLoginAction;
use TYPO3\CMS\Core\SysLog\Error as SystemLogErrorClassification;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class PasswordReset extends \TYPO3\CMS\Backend\Authentication\PasswordReset
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    public function resetPassword(ServerRequestInterface $request, Context $context): bool
    {
        $expirationTimestamp = (int)($request->getQueryParams()['e'] ?? '');
        $identityHash = (string)($request->getQueryParams()['i'] ?? '');
        $token = (string)($request->getQueryParams()['t'] ?? '');
        $newPassword = (string)$request->getParsedBody()['password'];
        $newPasswordRepeat = (string)$request->getParsedBody()['passwordrepeat'];
        $validationResults = $this->validatePassword($newPassword, $newPasswordRepeat);

        if (true === $validationResults->hasErrors()) {
            $this->logger->debug('Password reset not possible due to weak password');

            return false;
        }

        $user = $this->findValidUserForToken($token, $identityHash, $expirationTimestamp);
        if (null === $user) {
            $this->logger->warning('Password reset not possible. Valid user for token not found.');

            return false;
        }

        $userId = (int)$user['uid'];

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_users')
            ->update(
                'be_users',
                [
                    'password_reset_token' => '',
                    'password' => $this->getHasher()->getHashedPassword($newPassword),
                    'tx_mebackendsecurity_lastpasswordchange' => time() + (int)date('Z'),
                ],
                [
                    'uid' => $userId,
                ]
            );

        $this->logger->info('Password reset successful for user ' . $userId);
        $this->log(
            'Password reset successful for user %s',
            SystemLogLoginAction::PASSWORD_RESET_ACCOMPLISHED,
            SystemLogErrorClassification::SECURITY_NOTICE,
            $userId,
            [
                'email' => $user['email'],
                'user' => $userId
            ],
            NormalizedParams::createFromRequest($request)->getRemoteAddress(),
            $context
        );

        return true;
    }

    private function validatePassword(string $password, string $password2): Result
    {
        $requestParameters = ['password' => $password, 'password2' => $password2];

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(ExtensionConfiguration::EXT_KEY)
        );

        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);

        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = CompositeValidatorFactory::create(
            $this->objectManager,
            $extensionConfiguration,
            $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            )
        );

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters
        );

        return $compositeValidator->validate($passwordChangeRequest);
    }
}
