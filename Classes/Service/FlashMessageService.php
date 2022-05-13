<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Service;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class FlashMessageService
{
    protected FlashMessageQueue $messageQueue;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->messageQueue = GeneralUtility::makeInstance(
            FlashMessageQueue::class,
            ['core.template.flashMessages']
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function addEqualPasswordFlashMessage(): void
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

    /**
     * @codeCoverageIgnore
     */
    public function addPasswordErrorFlashMessage(Result $validationResult): void
    {
        $errorMessages = [];
        $messageTitle = LocalizationUtility::translate(
            'error.title',
            'me_backend_security'
        );

        foreach ($validationResult->getErrors() as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $flashMessage = new FlashMessage(
            implode(' | ', $errorMessages),
            $messageTitle,
            FlashMessage::ERROR,
            true
        );

        $this->messageQueue->addMessage(
            $flashMessage
        );
    }
}
