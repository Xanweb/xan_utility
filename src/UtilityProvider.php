<?php
namespace XanUtility;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class UtilityProvider extends ServiceProvider
{
    public function register()
    {
        $aliases = [
            'excel/export' => Service\Excel\Export::class,
            'excel/import' => Service\Excel\Import::class,
        ];

        foreach ($aliases as $alias => $class) {
            $this->app->alias($class, $alias);
        }
    }
}
