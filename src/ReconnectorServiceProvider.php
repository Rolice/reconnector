<?php
namespace Rolice\Reconnector;

use Illuminate\Support\ServiceProvider;
use Rolice\Reconnector\Connectors\ConnectionFactory;

/**
 * ReconnectorServiceProvider for Laravel 5.1+
 *
 * @package    Rolice\Reconnector
 * @version    1.0
 */
class ReconnectorServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });
    }

}