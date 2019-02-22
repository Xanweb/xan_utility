<?php
namespace XanUtility;

use Concrete\Core\Asset\AssetList;
use XanUtility\Asset\UtilityAssetList;

class AssetProvider
{
    private static $alreadyRegistered = false;

    public static function register()
    {
        if (self::$alreadyRegistered) {
            return;
        }

        $al = AssetList::getInstance();
        $al->registerMultiple([
            'xan/utility/global' => [
                ['javascript-localized', '/js/xan/utility/global.js', ['minify' => true, 'combine' => true]],
            ],
        ]);

        UtilityAssetList::registerMultipleCss([
            ['xan/item-list', 'css/item-list.css', ['combine' => true]],
        ]);

        UtilityAssetList::registerMultipleJavascript([
            ['xan/utility', 'js/utility.min.js', ['combine' => true]],
            ['xan/item-list', 'js/item-list.min.js', ['combine' => true]],
            ['xan/sitemap', 'js/selector/page.min.js', ['combine' => true]],
            ['xan/file-manager', 'js/selector/file.min.js', ['combine' => true]],
            ['xan/alert/dialog', 'js/alert.dialog.min.js', ['combine' => true]],
        ]);

        $al->registerGroupMultiple([
            'xan/item-list' => [
                [
                    ['javascript', 'jquery'],
                    ['javascript', 'underscore'],
                    ['javascript-localized', 'xan/utility/global'],
                    ['vendor-javascript', 'xan/item-list'],
                    ['vendor-css', 'xan/item-list'],
                ],
            ],
            'xan/sitemap' => array_merge(self::getAssetGroupAssets('core/sitemap'), [
                [
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/sitemap'],
                ]
            ]),
            'xan/file-manager' => array_merge(self::getAssetGroupAssets('core/file-manager'), [
                [
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/file-manager'],
                ]
            ]),
            'xan/alert/dialog' => [
                [
                    ['css', 'core/app'],
                    ['css', 'jquery/ui'],
                    ['css', 'fancytree'],
                    ['css', 'selectize'],
                    ['javascript', 'core/events'],
                    ['javascript', 'bootstrap/tooltip'],
                    ['javascript', 'underscore'],
                    ['javascript', 'backbone'],
                    ['javascript', 'jquery/ui'],
                    ['javascript-localized', 'jquery/ui'],
                    ['javascript', 'fancytree'],
                    ['javascript', 'selectize'],
                    ['javascript-localized', 'fancytree'],
                    ['javascript-localized', 'core/localization'],
                    ['javascript', 'core/app'],
                    ['vendor-javascript', 'xan/alert/dialog'],
                ],
            ],
        ]);

        self::$alreadyRegistered = true;
    }

    /**
     * Get AssetGroup Assets
     * @param string $assetGroupHandle
     *
     * @return array
     */
    private static function getAssetGroupAssets($assetGroupHandle)
    {
        return c5app('config')->get("app.asset_groups.{$assetGroupHandle}");
    }
}
