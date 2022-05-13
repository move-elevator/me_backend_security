<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Repository;

use MoveElevator\MeBackendSecurity\Utility\DateTimeUtility;
use PDO;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserRepository
{
    public const TABLE_NAME = 'be_users';
    public const LAST_CHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';
    public const LAST_LOGIN_COLUMN_NAME = 'lastlogin';

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

    public function updateLastChange(int $uid, int $lastChangeTimestamp): void
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
            ->set(self::LAST_CHANGE_COLUMN_NAME, $lastChangeTimestamp)
            ->execute();
    }

    public function updateLastChangeAndLogin(int $uid, int $lastLoginTimestamp): void
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
            ->set(self::LAST_LOGIN_COLUMN_NAME, $lastLoginTimestamp)
            ->set(self::LAST_CHANGE_COLUMN_NAME, 1)
            ->execute();
    }

    public function updatePassword(int $uid, string $hashedPassword): void
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
            ->set('password', $hashedPassword)
            ->set(self::LAST_CHANGE_COLUMN_NAME, DateTimeUtility::getTimestamp())
            ->execute();
    }

    public function isUserPresent(int $uid, string $username): bool
    {
        $queryBuilder = $this->getQueryBuilder();

        $isUserPresent = $queryBuilder
            ->count('uid')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $uid),
                $queryBuilder->expr()->eq('username', ':u')
            )
            ->setParameter('u', $username)
            ->execute()
            ->fetchOne();

        return false !== $isUserPresent;
    }
}
