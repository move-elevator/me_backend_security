<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Repository\BackendUserRepository;
use MoveElevator\MeBackendSecurity\Utility\DateTimeUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class BackendUserTableHook
{
    protected BackendUserRepository $backendUserRepository;
    protected FlashMessageQueue $messageQueue;
    protected PasswordHashInterface $passwordHashInstance;
    protected string $newPasswordPlain = '';
    protected string $currentPassword = '';

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->messageQueue = GeneralUtility::makeInstance(
            FlashMessageQueue::class,
            ['core.template.flashMessages']
        );

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
            $this->addFlashMessage();
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

    /**
     * @codeCoverageIgnore
     */
    protected function addFlashMessage(): void
    {
        $flashMessage = new FlashMessage(
            LocalizationUtility::translate(
                'error.1513850698',
                'me_backend_security'
            ),
            LocalizationUtility::translate(
                'error.title',
                'me_backend_security'
            ),
            FlashMessage::ERROR,
            true
        );

        $this->messageQueue->addMessage(
            $flashMessage
        );
    }
}
