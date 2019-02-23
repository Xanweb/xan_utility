<?php
namespace XanUtility\Application;

abstract class App
{

    /**
     * Get current package handle.
     *
     * @return string
     */
    public static function pkgHandle()
    {
        throw new \Exception(get_called_class(). ':' . __METHOD__ . ' need to be implemented');
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

    public static function setupAlias()
    {
        $aliasList = \Concrete\Core\Foundation\ClassAliasList::getInstance();
        $aliasList->register(static::getPackageAlias(), get_called_class());
    }

    protected static function getPackageAlias()
    {
        return camelcase(static::pkgHandle());
    }
}
