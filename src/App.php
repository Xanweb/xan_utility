<?php
namespace XanUtility;

use Concrete\Core\Support\Facade\Facade;

abstract class App
{
    private static $pkgHandle;
    private static $pkg;

    /**
     * Get current package handle
     *
     * @return string
     */
    public static function pkgHandle()
    {
        if (empty(self::$pkgHandle)) {
            self::$pkgHandle = uncamelcase(static::getPackageAlias());
        }
        return self::$pkgHandle;
    }

    /**
     * Get current package object
     * 
     * @return \Package
     */
    public static function pkg()
    {
        if (!is_object(self::$pkg)) {
            self::$pkg = Facade::getFacadeApplication()
                ->make('Concrete\Core\Package\PackageService')
                ->getByHandle(self::pkgHandle());
        }
        return self::$pkg;
    }

    /**
     * Gets a package specific entity manager.
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public static function em()
    {
        return Facade::getFacadeApplication()->make('database/orm')->entityManager();
    }

    /**
     * Get Xanweb Config
     */
    public static function cfg($name)
    {
        return Facade::getFacadeApplication()->make('config')->get('xanweb.'.$name);
    }

    /**
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function getFileConfig()
    {
        return self::pkg()->getFileConfig();
    }

    public static function setupAlias()
    {
        $aliasList = \Concrete\Core\Foundation\ClassAliasList::getInstance();
        $aliasList->register(static::getPackageAlias(), get_called_class());
    }

    protected static function getPackageAlias()
    {
        $reflector = new ReflectionClass(get_called_class());
        $ns = explode('\\', $reflector->getNamespaceName());
        return end($ns);
    }
    
    
}
