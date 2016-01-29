<?php
namespace Rolice\Reconnector\Connectors;

use PDO;
use App;
use Config;
use Exception;
use PDOException;
use Illuminate\Support\Arr;
use Illuminate\Database\Connectors\MySqlConnector as LaravelMySqlConnector;

class MySqlConnector extends LaravelMySqlConnector
{

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param  array $config
     * @return array|string
     */
    protected function getHostDsn(array $config)
    {
        extract($config, EXTR_SKIP);

        /**
         * @var string $host
         * @var int $port
         * @var string $database
         * @return string
         */

        $dsn = function ($host, $port, $database) {
            return "mysql:host={$host};" . (isset($port) ? "port={$port};" : '') . "dbname={$database}";
        };

        if (!is_array($host)) {
            return $dsn($host, $port, $database);
        }

        $result = [];

        /**
         * @var array $host
         * @return array
         */
        foreach ($host as $h) {
            $result[] = $dsn($h, $port, $database);
        }

        return $result;
    }

    /**
     * Create a new PDO connection.
     *
     * @param  string $dsn
     * @param  array $config
     * @param  array $options
     * @return PDO
     * @throws Exception
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

            $this->isClusterNodeReady($pdo);

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

    /**
     * Checks if PDO connection is made with "ready" server (check for clusters).
     * @param PDO $pdo
     * @throws PDOException
     */
    private function isClusterNodeReady(PDO $pdo)
    {
        if ('local' === App::environment() || !Config::get('reconnector.clustered')) {
            return;
        }

        if ('PRIMARY' !== mb_strtoupper($pdo->query("SHOW status LIKE 'wsrep_cluster_status'")->fetchColumn(1))) {
            throw new PDOException('MySQL node is a part of not nonoperational component.');
        }
    }
}