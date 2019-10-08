<?php
namespace XanUtility;

use XanUtility\Application\C5Runner;
use XanUtility\Form\FormServiceProvider;
use XanUtility\Migration\ServiceProvider as MigrationServiceProvider;

class Runner extends C5Runner
{
    protected static $started = false;

    public static function boot()
    {
        if (static::$started) {
            return;
        }

        parent::boot();

        AssetProvider::register();

        static::$started = true;
    }

    /**
     * @return array
     */
    protected static function getServiceProviders()
    {
        return [
            UtilityProvider::class,
            FormServiceProvider::class,
            MigrationServiceProvider::class,
        ];
    }

    /**
     * @return array
     */
    protected static function getRoutesClasses()
    {
        return ['XanUtility\Route\RouteList'];
    }
}
