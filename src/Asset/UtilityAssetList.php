<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\AssetList as CoreAssetList;

class UtilityAssetList
{
    public static function registerJavascript($assetHandle, $filename, $args = [])
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

    public static function registerMultipleJavascript(array $assets)
    {
        foreach ($assets as $settings) {
            call_user_func_array([self::class, 'registerJavascript'], $settings);
        }
    }

    public static function registerCss($assetHandle, $filename, $args = [])
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

        $o = new VendorCssAsset($assetHandle);
        $o->register($filename, $args);
        $al->registerAsset($o);

        return $o;
    }

    public static function registerMultipleCss(array $assets)
    {
        foreach ($assets as $settings) {
            call_user_func_array([self::class, 'registerCss'], $settings);
        }
    }
}
