<?php
namespace XanUtility\Module;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Foundation\ClassAliasList;
use Concrete\Core\Foundation\Service\ProviderList;
use XanUtility\Application\StaticApplicationTrait;

abstract class C5Module implements Module
{
    use StaticApplicationTrait;

    /**
     * Class to be used Statically
     */
    private function __construct()
    {
         return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see Module::pkg()
     */
    public static function pkg()
    {
        $pkg = null;
        $app = self::app();

        $cache = $app->make('cache/request');
        $item = $cache->getItem(sprintf('/package/handle/%s', static::pkgHandle()));
        if (!$item->isMiss()) {
            $pkg = $item->get();
        } else {
            $pkg = $app->make('Concrete\Core\Package\PackageService')
                ->getByHandle(static::pkgHandle());

            $cache->save($item->set($pkg));
        }

        return $pkg;
    }

    /**
     * {@inheritdoc}
     *
     * @see Module::config()
     */
    public static function config()
    {
        return static::pkg()->getController()->getConfig();
    }

    /**
     * {@inheritdoc}
     *
     * @see Module::boot()
     */
    public static function boot()
    {
        $aliases = static::getClassAliases();
        if(!empty($aliases)) {
            $aliasList = ClassAliasList::getInstance();
            $aliasList->registerMultiple($aliases);
        }

        $app = self::app();
        $providers = static::getServiceProviders();
        if(is_array($providers) && !empty($providers)) {
            $app->make(ProviderList::class)->registerProviders($providers);
        }

        $routeListClass = static::getRoutesClass();
        if(!empty($routeListClass)) {
            if(is_subclass_of($routeListClass, RouteListInterface::class)) {
                $app->call("$routeListClass@loadRoutes");
            } else {
                throw new \Exception(t(get_called_class() . ':getRoutesClass: RoutesClass should be instanceof \Concrete\Core\Routing\RouteListInterface'));
            }
        }
    }

    /**
     * Classes to be registered as aliases in \Concrete\Core\Foundation\ClassAliasList
     * @return array
     */
    protected static function getClassAliases()
    {
        return [
            static::getPackageAlias() => get_called_class()
        ];
    }

    /**
     * Get Package Alias
     * @return string
     */
    protected static function getPackageAlias()
    {
        return camelcase(static::pkgHandle());
    }

    /**
     * Get Service Providers Class Names
     * @return array
     */
    protected static function getServiceProviders()
    {
        return [];
    }

    /**
     * Get Class name for RouteList, must be instance of \Concrete\Core\Routing\RouteListInterface
     * @return string
     */
    protected static function getRoutesClass()
    {
        return;
    }
}
