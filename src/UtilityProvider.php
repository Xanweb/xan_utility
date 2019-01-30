<?php
namespace XanUtility;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\User\User;

class UtilityProvider extends ServiceProvider
{
    public function register()
    {
        $aliases = [
            'database/connection' => 'Concrete\Core\Database\Connection\Connection',
            'excel/export' => Service\Excel\Export::class,
            'excel/import' => Service\Excel\Import::class,
        ];

        foreach ($aliases as $alias => $class) {
            $this->app->alias($class, $alias);
        }

        if(!$this->app->bound(User::class)) {
            $this->app->singleton([User::class => 'user/current']);
        }
    }
}
