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

        UtilityAssetList::registerMultiple([
            'xan/utility' => [
                ['js/utility.min.js', ['combine' => true]],
            ],
            'xan/item-list' => [
                ['js/item-list.min.js', ['combine' => true]],
            ],
            'xan/selector/page' => [
                ['js/selector/page.min.js', ['combine' => true]],
            ],
            'xan/selector/file' => [
                ['js/selector/file.min.js', ['combine' => true]],
            ],
            'xan/alert/dialog' => [
                ['js/alert.dialog.min.js', ['combine' => true]],
            ],
        ]);

        $al->registerGroupMultiple([
            'xan/item-list' => [
                [
                    ['javascript', 'jquery'],
                    ['javascript', 'underscore'],
                    ['javascript-localized', 'xan/utility/global'],
                    ['vendor-javascript', 'xan/item-list'],
                ],
            ],
            'xan/selector/page' => [
                [
                    ['javascript', 'jquery'],
                    ['css', 'core/app'],
                    ['css', 'jquery/ui'],
                    ['css', 'core/file-manager'],
                    ['css', 'fancytree'],
                    ['css', 'selectize'],
                    ['css', 'core/sitemap'],
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
                    ['javascript', 'jquery/fileupload'],
                    ['javascript', 'core/sitemap'],
                    ['javascript', 'core/tree'],
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/selector/page'],
                ],
            ],
            'xan/selector/file' => [
                [
                    ['javascript', 'jquery'],
                    ['css', 'core/app'],
                    ['css', 'jquery/ui'],
                    ['css', 'core/file-manager'],
                    ['css', 'fancytree'],
                    ['css', 'selectize'],
                    ['css', 'core/sitemap'],
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
                    ['javascript', 'jquery/fileupload'],
                    ['javascript', 'core/sitemap'],
                    ['javascript', 'core/tree'],
                    ['javascript', 'core/file-manager'],
                    ['vendor-javascript', 'xan/utility'],
                    ['vendor-javascript', 'xan/selector/file'],
                ],
            ],
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
}
