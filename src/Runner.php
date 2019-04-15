<?php
namespace XanUtility;

use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Foundation\Service\ProviderList;
use XanUtility\Application\StaticApplicationTrait;
use XanUtility\Controller\Frontend\XanBase;
use XanUtility\Form\FormServiceProvider;

class Runner
{
    use StaticApplicationTrait;

    protected static $started = false;

    public static function boot()
    {
        if (static::$started) {
            return;
        }

        $app = self::app();
        $providers = $app->make(ProviderList::class);
        $providers->registerProviders([
            UtilityProvider::class,
            FormServiceProvider::class
        ]);

        $router = $app->make(RouterInterface::class);
        $router->registerMultiple([
            '/js/xan/utility/global.js' => [XanBase::class . '::getJavascript'],
        ]);

        AssetProvider::register();

        static::$started = true;
    }
}
