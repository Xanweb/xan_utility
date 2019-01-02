<?php
namespace XanUtility\Application;

abstract class App
{
    private static $pkgHandle;
    private static $pkg;

    /**
     * Get current package handle.
     *
     * @return string
     */
    public static function pkgHandle()
    {
        if (!self::$pkgHandle) {
            $class = new \ReflectionClass(get_called_class());
            $path = str_replace(DIR_PACKAGES . '/', '', str_replace(DIRECTORY_SEPARATOR, '/', $class->getFilename()));
            self::$pkgHandle = reset(explode('/', $path));
        }

        return self::$pkgHandle;
    }

    /**
     * Get current package object.
     *
     * @return \Concrete\Core\Entity\Package
     */
    public static function pkg()
    {
        if (!is_object(self::$pkg)) {
            self::$pkg = c5app()
                ->make('Concrete\Core\Package\PackageService')
                ->getByHandle(self::pkgHandle());
        }

        return self::$pkg;
    }

    /**
     * Get Package Database Config.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function config()
    {
        return self::pkg()->getController()->getConfig();
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
