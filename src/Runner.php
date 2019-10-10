<?php
namespace XanUtility;

use XanUtility\Application\C5Runner;
use XanUtility\Form\FormServiceProvider;
use XanUtility\Migration\ServiceProvider as MigrationServiceProvider;

class Runner extends C5Runner
{
    protected static $started = false;

    /**
     * Boot Up Utility Services.
     *
     * @throws
     */
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
     * {@inheritdoc}
     *
     * @see C5Runner::getServiceProviders()
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
     * {@inheritdoc}
     *
     * @see C5Runner::getRoutesClasses()
     */
    protected static function getRoutesClasses()
    {
        return ['XanUtility\Route\RouteList'];
    }

    /**
     * {@inheritdoc}
     *
     * @see C5Runner::getClassAliases()
     */
    protected static function getClassAliases()
    {
        return [
            'MultilingualSection' => 'Concrete\Core\Multilingual\Page\Section\Section',
        ];
    }
}
