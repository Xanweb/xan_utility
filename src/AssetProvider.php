<?php
namespace XanUtility;

use XanUtility\Asset\AssetList;

class AssetProvider
{
    private static $alreadyRegistered = false;

    public static function register()
    {
        if (self::$alreadyRegistered) {
            return;
        }

        AssetList::registerMultiple([
            'xan-utility' => [
                ['vendor-javascript', 'js/utility.js', ['minify' => true, 'combine' => true]],
            ],
            'xan-utility/itemlist' => [
                ['vendor-javascript', 'js/itemlist.js', ['minify' => true, 'combine' => true]],
            ],
            'xan-utility/selector/page' => [
                ['vendor-javascript', 'js/selector/page.js', ['minify' => true, 'combine' => true]],
            ],
            'xan-utility/selector/file' => [
                ['vendor-javascript', 'js/selector/file.js', ['minify' => true, 'combine' => true]],
            ],
            'xan-utility/alert/dialog' => [
                ['vendor-javascript', 'js/alert.dialog.js', ['minify' => true, 'combine' => true]],
            ],
        ]);

        AssetList::registerGroupMultiple([
            'xan-utility/itemlist' => [
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
                    ['vendor-javascript', 'xan-utility'],
                    ['vendor-javascript', 'xan-utility/itemlist'],
                ],
            ],
            'xan-utility/selector/page' => [
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
                    ['vendor-javascript', 'xan-utility'],
                    ['vendor-javascript', 'xan-utility/selector/page'],
                ],
            ],
            'xan-utility/selector/file' => [
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
                    ['vendor-javascript', 'xan-utility'],
                    ['vendor-javascript', 'xan-utility/selector/file'],
                ],
            ],
            'xan-utility/alert/dialog' => [
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
                    ['vendor-javascript', 'xan-utility/alert/dialog'],
                ],
            ],
        ]);
        self::$alreadyRegistered = true;
    }
}
