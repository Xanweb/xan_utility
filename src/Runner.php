<?php
namespace XanUtility;

use Concrete\Core\Foundation\Service\ProviderList;
use Concrete\Core\Routing\RouterInterface;
use XanUtility\Controller\Frontend\XanBase;

class Runner
{
    protected static $started = false;

    public static function boot()
    {
        if (static::$started) {
            return;
        }

        $providers = c5app()->make(ProviderList::class);
        $providers->registerProvider(UtilityProvider::class);

        $router = c5app()->make(RouterInterface::class);
        $router->registerMultiple([
            '/js/xan/utility/global.js' => [XanBase::class . '::getJavascript'],
        ]);

        AssetProvider::register();

        static::$started = true;
    }
}
