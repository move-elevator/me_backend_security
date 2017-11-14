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

        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = new DatabaseConnection();

        $databaseConnection->setDatabaseUsername($databaseConfiguration['username']);
        $databaseConnection->setDatabasePassword($databaseConfiguration['password']);
        $databaseConnection->setDatabaseHost($databaseConfiguration['host']);
        $databaseConnection->setDatabasePort($databaseConfiguration['port']);
        $databaseConnection->setDatabaseName($databaseConfiguration['database']);

        if (empty($databaseConfiguration['socket']) === false) {
            $databaseConnection->setDatabaseSocket($databaseConfiguration['socket']);
        }

        $databaseConnection->initialize();
        $databaseConnection->connectDB();

        return $databaseConnection;
    }
}
