<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\AssetList as CoreAssetList;

class AssetList
{
    public static function register($assetType, $assetHandle, $filename, $args = [])
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
        foreach ($assets as $asset_handle => $asset_types) {
            foreach ($asset_types as $asset_type => $asset_settings) {
                array_splice($asset_settings, 1, 0, $asset_handle);
                call_user_func_array([self::class, 'register'], $asset_settings);
            }
        }
    }

    public static function registerGroupMultiple(array $asset_groups)
    {
        $al = CoreAssetList::getInstance();

        return $al->registerGroupMultiple($asset_groups);
    }
}
