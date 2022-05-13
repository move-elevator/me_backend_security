<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Repository\BackendUserRepository;
use MoveElevator\MeBackendSecurity\Service\FlashMessageService;
use MoveElevator\MeBackendSecurity\Utility\DateTimeUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserTableHook
{
    protected BackendUserRepository $backendUserRepository;
    protected FlashMessageService $flashMessageService;
    protected PasswordHashInterface $passwordHashInstance;
    protected string $newPasswordPlain = '';
    protected string $currentPassword = '';

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $this->backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
        $this->passwordHashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)
            ->getDefaultHashInstance('BE');
    }

    /**
     * @codeCoverageIgnore
     */
    public function processDatamap_preProcessFieldArray(
        &$incomingFieldArray,
        $table,
        $id,
        &$pObj
    ): void {
        if (BackendUserRepository::TABLE_NAME !== $table) {
            return;
        }

        if (false === isset($incomingFieldArray['password'])) {
            return;
        }

        if (false === is_int($id)) {
            return;
        }

        $this->newPasswordPlain = $incomingFieldArray['password'];
        $this->currentPassword = $this->backendUserRepository->findPasswordByUid($id);
    }

    /**
     * @codeCoverageIgnore
     */
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $id,
        &$fieldArray,
        &$tcemain
    ): void {
        $lastChange = DateTimeUtility::getTimeStamp();

        if (BackendUserRepository::TABLE_NAME !== $table) {
            return;
        }

        if (false === isset($fieldArray['password'])) {
            return;
        }

        if (false === is_int($id)) {
            return;
        }

        $isOldPasswordTheSameIgnoreIt = $this->passwordHashInstance->checkPassword(
            $this->newPasswordPlain,
            $this->currentPassword
        );

        if (true === $isOldPasswordTheSameIgnoreIt) {
            $this->flashMessageService->addEqualPasswordFlashMessage();
            return;
        }

        // If user is created or copied or current user is not the same as account
        if ('insert' === $status ||
            $id !== (int)$GLOBALS['BE_USER']->user['uid']
        ) {
            $lastChange = 1;
        }

        $this->backendUserRepository->updateLastChange($id, $lastChange);
    }
}
