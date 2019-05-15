<?php
namespace XanUtility;

use Concrete\Core\Foundation\Service\ProviderList;
use XanUtility\Application\StaticApplicationTrait;
use XanUtility\Form\FormServiceProvider;

class Runner
{
    use StaticApplicationTrait;

    protected static $started = false;

    public static function boot()
    {
        if (static::$started) {
            return;
        }

        $app = self::app();
        $providers = $app->make(ProviderList::class);
        $providers->registerProviders([
            UtilityProvider::class,
            FormServiceProvider::class
        ]);

        $app->call('XanUtility\Route\RouteList@loadRoutes');

        AssetProvider::register();

        static::$started = true;
    }
}
