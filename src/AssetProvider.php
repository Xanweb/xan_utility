<?php
namespace XanUtility;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\AssetPointer;
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
            'xan/sitemap' => self::mergeAssets(self::getAssetGroupAssets('core/sitemap'), [
                [
                    ['javascript-localized', 'xan/utility/global'],
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/sitemap'],
                ],
            ]),
            'xan/file-manager' => self::mergeAssets(self::getAssetGroupAssets('core/file-manager'), [
                [
                    ['javascript-localized', 'xan/utility/global'],
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/file-manager'],
                ],
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
                    ['javascript-localized', 'xan/utility/global'],
                ],
            ],
        ]);

        // Override Core/Sitemap
        $assetGrp = $al->getAssetGroup('core/sitemap');
        $assetGrp->add(new AssetPointer('javascript-localized', 'xan/utility/global'));
        $assetGrp->add(new AssetPointer('vendor-javascript', 'xan/utility'));
        $assetGrp->add(new AssetPointer('vendor-javascript', 'xan/sitemap'));

        $assetGrp = $al->getAssetGroup('core/file-manager');
        $assetGrp->add(new AssetPointer('javascript-localized', 'xan/utility/global'));
        $assetGrp->add(new AssetPointer('vendor-javascript', 'xan/utility'));
        $assetGrp->add(new AssetPointer('vendor-javascript', 'xan/file-manager'));

        self::$alreadyRegistered = true;
    }

    /**
     * Get AssetGroup Assets.
     *
     * @param string $assetGroupHandle
     *
     * @return array
     */
    private static function getAssetGroupAssets($assetGroupHandle)
    {
        return c5app('config')->get("app.asset_groups.{$assetGroupHandle}");
    }

    private static function mergeAssets(array $assets1, array $assets2)
    {
        $limit = max(count($assets1), count($assets2));
        $result = [];

        for ($i = 0; $i < $limit; ++$i) {
            if (isset($assets1[$i]) && isset($assets2[$i])) {
                $result[$i] = array_merge($assets1[$i], $assets2[$i]);
            } elseif (isset($assets1[$i])) {
                $result[$i] = $assets1[$i];
            } else {
                $result[$i] = $assets2[$i];
            }
        }

        return $result;
    }
}
