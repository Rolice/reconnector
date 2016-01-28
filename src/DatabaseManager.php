<?php
namespace Rolice\Reconnector;

use Rolice\Reconnector\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager as LaravelDatabaseManager;

/**
 * Class DatabaseManager
 * Replacement class for default Laravel DatabaseManager. It only changed factory through DI.
 * @package Rolice\Reconnector
 */
class DatabaseManager extends LaravelDatabaseManager
{

    /**
     * Create a new database manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
     */
    public function __construct($app, ConnectionFactory $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
    }

}