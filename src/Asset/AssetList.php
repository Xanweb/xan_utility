<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\AssetList as CoreAssetList;

class AssetList
{
    public static function register($assetType, $assetHandle, $filename, $args = [], $pkg = false)
    {
        $al = CoreAssetList::getInstance();
        $class = '\\XanUtility\\Asset\\' . Object::camelcase($assetType) . 'Asset';
        if (!class_exists($class)) {
            return $al->register($assetType, $assetHandle, $filename, $args);
        }

        $defaults = [
            'position' => false,
            'local' => true,
            'version' => false,
            'combine' => -1,
            'minify' => -1, // use the asset default
        ];
        // overwrite all the defaults with the arguments
        $args = array_merge($defaults, $args);

        $o = new $class($assetHandle);
        $o->register($filename, $args, $pkg);
        $al->registerAsset($o);

        return $o;
    }

    public static function registerMultiple(array $assets)
    {
        foreach ($assets as $handle => $types) {
            foreach ($types as $settings) {
                array_splice($settings, 1, 0, $handle);
                call_user_func_array([self::class, 'register'], $settings);
            }
        }
    }

    public static function registerGroupMultiple(array $assetGroups)
    {
        $al = CoreAssetList::getInstance();

        return $al->registerGroupMultiple($assetGroups);
    }
}
