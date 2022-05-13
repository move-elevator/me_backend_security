<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Repository;

use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserRepository
{
    public const TABLE_NAME = 'be_users';
    public const LAST_CHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';

    public function getQueryBuilder(): QueryBuilder
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);
    }

    public function findPasswordByUid(int $uid): string
    {
        $queryBuilder = $this->getQueryBuilder();

        return (string)$queryBuilder
            ->select('password')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchOne();
    }

    public function updateLastChange(int $uid, int $timestamp): void
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->update(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                )
            )
            ->set(self::LAST_CHANGE_COLUMN_NAME, $timestamp)
            ->execute();
    }
}
