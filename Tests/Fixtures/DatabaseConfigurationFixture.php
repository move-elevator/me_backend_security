<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixtures
 */
trait DatabaseConfigurationFixture
{
    /**
     * @var string
     */
    protected $username = 'foo';

    /**
     * @var string
     */
    protected $password = 'bar';

    /**
     * @var string
     */
    protected $host = '127.0.0.1';

    /**
     * @var string
     */
    protected $port = '3306';

    /**
     * @var string
     */
    protected $database = 'foobar_db';

    /**
     * @var string
     */
    protected $socket = '/tmp/mysql/mysql.sock';

    /**
     * @return array
     */
    protected function getRawDatabaseConfigurationFixture()
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'socket' => $this->socket
        ];
    }
}
