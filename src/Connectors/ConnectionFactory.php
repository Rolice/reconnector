<?php
namespace Rolice\Reconnector\Connectors;

use InvalidArgumentException;
use Illuminate\Database\Connectors;
use Illuminate\Database\Connectors\ConnectionFactory as LaravelConnectionFactory;
use Rolice\Reconnector\Connectors\MySqlConnector;

class ConnectionFactory extends LaravelConnectionFactory
{

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array $config
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     * @throws InvalidArgumentException
     */
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }

        switch ($config['driver']) {
            case 'mysql':
                return new MySqlConnector;

            case 'pgsql':
                return new Connectors\PostgresConnector;

            case 'sqlite':
                return new Connectors\SQLiteConnector;

            case 'sqlsrv':
                return new Connectors\SqlServerConnector;
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

}