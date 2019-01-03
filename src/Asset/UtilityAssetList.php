<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\AssetList as CoreAssetList;

class UtilityAssetList
{
    public static function register($assetHandle, $filename, $args = [])
    {
        $al = CoreAssetList::getInstance();
        $defaults = [
            'position' => false,
            'local' => true,
            'version' => false,
            'combine' => -1,
            'minify' => -1, // use the asset default
        ];

        // overwrite all the defaults with the arguments
        $args = array_merge($defaults, $args);

        $o = new VendorJavascriptAsset($assetHandle);
        $o->register($filename, $args);
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

}
