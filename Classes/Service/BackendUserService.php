<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Service;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Domain\Repository\BackendUserRepository;
use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use MoveElevator\MeBackendSecurity\Utility\DateTimeUtility;
use MoveElevator\MeBackendSecurity\Utility\MfaUtility;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Extbase\Error\Result;

class BackendUserService
{
    protected const USER_DOES_NOT_EXIST_ERROR_CODE = 1510742747;
    protected const FIRST_CHANGE_MESSAGE_CODE = 1513928250;

    protected BackendUserAuthentication $backendUserAuthentication;
    protected BackendUserRepository $backendUserRepository;
    protected ExtensionConfiguration $extensionConfiguration;
    protected CompositeValidator $compositeValidator;
    protected PasswordHashInterface $passwordHashInstance;

    public function __construct(
        BackendUserAuthentication $backendUserAuthentication,
        BackendUserRepository $backendUserRepository,
        ExtensionConfiguration $extensionConfiguration,
        CompositeValidator $compositeValidator,
        PasswordHashInterface $passwordHashInstance
    ) {
        $this->backendUserAuthentication = $backendUserAuthentication;
        $this->backendUserRepository = $backendUserRepository;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->compositeValidator = $compositeValidator;
        $this->passwordHashInstance = $passwordHashInstance;
    }

    public function handlePasswordChangeRequest(PasswordChangeRequest $passwordChangeRequest): ?LoginProviderRedirect
    {
        $validationResults = $this->compositeValidator->validate($passwordChangeRequest);
        $userId = (int)$this->backendUserAuthentication->user['uid'];
        $username = $this->backendUserAuthentication->user['username'];

        if (true === $validationResults->hasErrors()) {
            return LoginProviderRedirectFactory::create(
                $username,
                $this->getErrorCodesWithArguments($validationResults),
                MfaUtility::getMfaToken($this->backendUserAuthentication)
            );
        }

        $isUserPresent = $this->backendUserRepository->isUserPresent(
            $userId,
            $username
        );

        if (false === $isUserPresent) {
            return LoginProviderRedirectFactory::create(
                $username,
                [self::USER_DOES_NOT_EXIST_ERROR_CODE],
                MfaUtility::getMfaToken($this->backendUserAuthentication)
            );
        }

        $this->backendUserRepository->updatePassword(
            $userId,
            $this->passwordHashInstance->getHashedPassword($passwordChangeRequest->getPassword())
        );

        $this->backendUserAuthentication->user[BackendUserRepository::LAST_CHANGE_COLUMN_NAME] =
            DateTimeUtility::getTimestamp();

        return null;
    }

    public function checkPasswordLifeTime(): ?LoginProviderRedirect
    {
        $userId = (int)$this->backendUserAuthentication->user['uid'];
        $username = $this->backendUserAuthentication->user['username'];

        $this->handleNewAccount();

        if (true === $this->isNonMigratedAccount()) {
            $this->backendUserRepository->migrate($userId, DateTimeUtility::getTimestamp());

            return null;
        }

        $lastPasswordChange = (int)$this->backendUserAuthentication
            ->user[BackendUserRepository::LAST_CHANGE_COLUMN_NAME];

        if (1 === $lastPasswordChange) {
            return LoginProviderRedirectFactory::create($username, [], MfaUtility::getMfaToken($this->backendUserAuthentication), [
                self::FIRST_CHANGE_MESSAGE_CODE,
            ]);
        }

        $validUntilInDays = $this->extensionConfiguration->getMaximumValidDays();

        if (false === DateTimeUtility::isDeadlineReached($validUntilInDays, $lastPasswordChange)) {
            return null;
        }

        return LoginProviderRedirectFactory::create($username, [], MfaUtility::getMfaToken($this->backendUserAuthentication));
    }

    private function handleNewAccount(): void
    {
        $lastLogin = (int)$this->backendUserAuthentication->user[BackendUserRepository::LAST_LOGIN_COLUMN_NAME];

        if ($lastLogin !== 0) {
            return;
        }

        $this->backendUserAuthentication->user[BackendUserRepository::LAST_LOGIN_COLUMN_NAME] = time();
        $this->backendUserAuthentication->user[BackendUserRepository::LAST_CHANGE_COLUMN_NAME] = 1;

        $this->backendUserRepository->updateLastChangeAndLogin(
            (int)$this->backendUserAuthentication->user['uid'],
            DateTimeUtility::getTimestamp()
        );
    }

    private function isNonMigratedAccount(): bool
    {
        return 0 === (int)$this->backendUserAuthentication->user[BackendUserRepository::LAST_CHANGE_COLUMN_NAME];
    }

    private function getErrorCodesWithArguments(Result $validationResults): array
    {
        $errors = [];

        foreach ($validationResults->getErrors() as $error) {
            $errors[] = [
                'errorCode' => $error->getCode(),
                'arguments' => $error->getArguments(),
            ];
        }

        return $errors;
    }
}
