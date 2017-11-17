<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use TYPO3\CMS\Core\Database\DatabaseConnection;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class DatabaseConnectionFactory
{
    /**
     * @param array $databaseConfiguration
     *
     * @return DatabaseConnection
     */
    public static function create($databaseConfiguration)
    {
        if (empty($databaseConfiguration['username']) ||
            empty($databaseConfiguration['password']) ||
            empty($databaseConfiguration['host']) ||
            empty($databaseConfiguration['port']) ||
            empty($databaseConfiguration['database'])
        ) {
            throw new \InvalidArgumentException(
                'The given arguments are incomplete!'
            );
        }

        return self::createDatabaseConnection($databaseConfiguration);
    }

    /**
     * @param array $databaseConfiguration
     *
     * @return DatabaseConnection
     */
    private static function createDatabaseConnection($databaseConfiguration)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = new DatabaseConnection();

        $databaseConnection->setDatabaseUsername((string) $databaseConfiguration['username']);
        $databaseConnection->setDatabasePassword((string) $databaseConfiguration['password']);
        $databaseConnection->setDatabaseHost((string) $databaseConfiguration['host']);
        $databaseConnection->setDatabasePort((string) $databaseConfiguration['port']);
        $databaseConnection->setDatabaseName((string) $databaseConfiguration['database']);

        if (empty($databaseConfiguration['socket']) === false) {
            $databaseConnection->setDatabaseSocket((string) $databaseConfiguration['socket']);
        }

        $databaseConnection->initialize();
        $databaseConnection->connectDB();

        if ($databaseConnection->isConnected() === false) {
            throw new \RuntimeException(
                'Could not connect to database server!'
            );
        }

        return $databaseConnection;
    }
}
