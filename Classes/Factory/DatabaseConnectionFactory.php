<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class DatabaseConnectionFactory
{
    /**
     * @param ObjectManager $objectManager
     * @param array $databaseConfiguration
     *
     * @return DatabaseConnection
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function create(ObjectManager $objectManager, array $databaseConfiguration)
    {
        if (empty($databaseConfiguration['username']) ||
            empty($databaseConfiguration['password']) ||
            empty($databaseConfiguration['host']) ||
            empty($databaseConfiguration['database'])
        ) {
            throw new \InvalidArgumentException(
                'Some parameters are missing in given database configuration.',
                1512481307
            );
        }

        return self::createDatabaseConnection($objectManager, $databaseConfiguration);
    }

    /**
     * @param ObjectManager $objectManager
     * @param array $databaseConfiguration
     *
     * @return DatabaseConnection
     */
    private static function createDatabaseConnection(ObjectManager $objectManager, array $databaseConfiguration)
    {
        /** @var \TYPO3\CMS\Core\Database\DatabaseConnection $databaseConnection */
        $databaseConnection = $objectManager->get(DatabaseConnection::class);

        $databaseConnection->setDatabaseUsername((string) $databaseConfiguration['username']);
        $databaseConnection->setDatabasePassword((string) $databaseConfiguration['password']);
        $databaseConnection->setDatabaseHost((string) $databaseConfiguration['host']);
        $databaseConnection->setDatabaseName((string) $databaseConfiguration['database']);

        if (empty($databaseConfiguration['port']) === false) {
            $databaseConnection->setDatabasePort((string) $databaseConfiguration['port']);
        }

        if (empty($databaseConfiguration['socket']) === false) {
            $databaseConnection->setDatabaseSocket((string) $databaseConfiguration['socket']);
        }

        $databaseConnection->initialize();
        $databaseConnection->connectDB();

        if ($databaseConnection->isConnected() === false) {
            throw new \RuntimeException(
                'Could not connect to database server.',
                1512481350
            );
        }

        return $databaseConnection;
    }
}
