<?php
namespace XanUtility\Module;


interface Module
{
    /**
     * Get current package handle.
     *
     * @return string
     */
    public static function pkgHandle();

    /**
     * Get current package object.
     *
     * @return \Concrete\Core\Entity\Package
     */
    public static function pkg();

    /**
     * Get Package Database Config.
     *
     * @return \Concrete\Core\Config\Repository\Liaison
     */
    public static function config();

    /**
     * Basic Boot for Module
     */
    public static function boot();
}