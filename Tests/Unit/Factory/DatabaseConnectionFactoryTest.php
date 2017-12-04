<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Factory\DatabaseConnectionFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\DatabaseConfigurationFixture;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class DatabaseConfigurationFactoryTest extends TestCase
{
    use DatabaseConfigurationFixture;

    protected $databaseConnection;

    public function setup()
    {
        $this->databaseConnection = \Mockery::mock(DatabaseConnection::class . '[initialize,connectDB,isConnected]');
        $this->databaseConnection
            ->shouldReceive('initialize');
        $this->databaseConnection
            ->shouldReceive('connectDB');
    }

    public function testCreateObjectFromValidArguments()
    {
        $this->databaseConnection
            ->shouldReceive('isConnected')
            ->withAnyArgs()
            ->andReturnTrue();

        $objectManager = \Mockery::mock(ObjectManager::class);
        $objectManager
            ->shouldReceive('get')
            ->withArgs([DatabaseConnection::class])
            ->andReturn($this->databaseConnection);

        $rawDatabaseConfiguration = $this->getRawDatabaseConfigurationFixture();

        $databaseConnection = DatabaseConnectionFactory::create($objectManager, $rawDatabaseConfiguration);

        $this->assertInstanceOf(DatabaseConnection::class, $databaseConnection);
    }

    public function testCreateObjectFromValidArgumentsWithConnectionFailed()
    {
        $this->databaseConnection
            ->shouldReceive('isConnected')
            ->withAnyArgs()
            ->andReturnFalse();

        $objectManager = \Mockery::mock(ObjectManager::class);
        $objectManager
            ->shouldReceive('get')
            ->withArgs([DatabaseConnection::class])
            ->andReturn($this->databaseConnection);

        $rawDatabaseConfiguration = $this->getRawDatabaseConfigurationFixture();

        $this->expectException(\RuntimeException::class);

        DatabaseConnectionFactory::create($objectManager, $rawDatabaseConfiguration);
    }
}
