<?php
namespace XanUtility\Module;

use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Foundation\Service\ProviderList;
use XanUtility\Application\StaticApplicationTrait;

abstract class C5Module
{
    use StaticApplicationTrait;

    /**
     * Get current package handle.
     *
     * @return string
     */
    public static function pkgHandle()
    {
        throw new \Exception(get_called_class() . ':' . __METHOD__ . ' need to be implemented');
    }

    /**
     * Get current package object.
     *
     * @return \Concrete\Core\Entity\Package
     */
    public static function pkg()
    {
        $pkg = null;

        $cache = c5app()->make('cache/request');
        $item = $cache->getItem(sprintf('/package/handle/%s', static::pkgHandle()));
        if (!$item->isMiss()) {
            $pkg = $item->get();
        } else {
            $pkg = c5app()
                ->make('Concrete\Core\Package\PackageService')
                ->getByHandle(static::pkgHandle());

            $cache->save($item->set($pkg));
        }

        return $pkg;
    }

    /**
     * Get Package Database Config.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function config()
    {
        return static::pkg()->getController()->getConfig();
    }

    /**
     *
     * @throws \Exception
     */
    public static function boot()
    {
        static::setupAlias();

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

    protected static function setupAlias()
    {
        $aliasList = \Concrete\Core\Foundation\ClassAliasList::getInstance();
        $aliasList->register(static::getPackageAlias(), get_called_class());
    }

    protected static function getPackageAlias()
    {
        return camelcase(static::pkgHandle());
    }
}
