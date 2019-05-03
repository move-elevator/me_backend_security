<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 */
class TableHook
{
    const USERS_TABLE = 'be_users';
    const LASTCHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';

    /**
     * @var FlashMessageQueue
     */
    protected $messageQueue;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var PasswordHashInterface
     */
    protected $passwordHashInstance;

    /**
     * @var string
     */
    protected $newPasswordPlain;

    /**
     * @var string
     */
    protected $currentPassword;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->messageQueue = $objectManager->get(FlashMessageQueue::class, 'core.template.flashMessages');

        /** @var ConnectionPool $connectionPool */
        $connectionPool = $objectManager->get(ConnectionPool::class);
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable(self::USERS_TABLE);

        $this->passwordHashInstance = $objectManager
            ->get(PasswordHashFactory::class)
            ->getDefaultHashInstance('BE');
    }

    /**
     * @param mixed $incomingFieldArray
     * @param mixed $table
     * @param mixed $id
     * @param mixed $pObj
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$pObj): void // @codingStandardsIgnoreLine
    {
        if ($table !== 'be_users') {
            return;
        }

        if (isset($incomingFieldArray['password']) === false) {
            return;
        }

        $this->newPasswordPlain = $incomingFieldArray['password'];
        $this->currentPassword = $this->queryBuilder
            ->select('password')
            ->from(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $id)
            )
            ->execute()
            ->fetchColumn(0);
    }

    /**
     * @param mixed $status
     * @param mixed $table
     * @param mixed $id
     * @param mixed $fieldArray
     * @param mixed $tcemain
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$tcemain): void // @codingStandardsIgnoreLine
    {
        $lastChange = time() + date('Z');

        if ($table !== 'be_users') {
            return;
        }

        if (isset($fieldArray['password']) === false) {
            return;
        }

        // If old password is the same, ignore it
        if ($this->passwordHashInstance->checkPassword($this->newPasswordPlain, $this->currentPassword)) {
            $this->addFlashMessage();
            return;
        }

        // If user is created or copied
        if ($status === 'insert') {
            $lastChange = 0;
        }

        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $id)
            )
            ->set(self::LASTCHANGE_COLUMN_NAME, $lastChange)
            ->execute();
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
