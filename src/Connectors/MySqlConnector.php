<?php
namespace Rolice\Reconnector\Connectors;

use PDO;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\Connectors\MySqlConnector as LaravelMySqlConnector;

class MySqlConnector extends LaravelMySqlConnector
{

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param  array $config
     * @return string
     */
    protected function getHostDsn(array $config)
    {
        extract($config, EXTR_SKIP);

        /**
         * @var string $host
         * @var int $port
         * @var string $database
         */

        $result = function ($host, $port, $database) {
            return "mysql:host={$host};" . (isset($port) ? "port={$port};" : '') . "dbname={$database}";
        };

        if (!is_array($host)) {
            return $result($host, $port, $database);
        }

        /**
         * @var array $host
         */
        foreach ($host as $h) {
            yield $result($h, $port, $database);
        }
    }

    /**
     * Create a new PDO connection.
     *
     * @param  string $dsn
     * @param  array $config
     * @param  array $options
     * @return \PDO
     */
    public function createConnection($dsn, array $config, array $options)
    {
        $username = Arr::get($config, 'username');

        $password = Arr::get($config, 'password');

        $create_pdo = function ($dsn, $username, $password, $options) {
            try {
                $pdo = new PDO($dsn, $username, $password, $options);
            } catch (Exception $e) {
                $pdo = $this->tryAgainIfCausedByLostConnection(
                    $e, $dsn, $username, $password, $options
                );
            }

            return $pdo;
        };

        if (is_array($dsn)) {
            foreach ($dsn as $idx => $dsn_string) {
                try {
                    return $create_pdo($dsn_string, $username, $password, $options);
                } catch (Exception $e) {
                    if (!$this->causedByLostConnection($e) || $idx >= count($dsn) - 1) {
                        throw $e;
                    }
                }
            }
        }

        return $create_pdo($dsn, $username, $password, $options);
    }

}