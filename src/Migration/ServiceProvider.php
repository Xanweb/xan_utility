<?php
namespace XanUtility\Migration;

use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton('XanUtility\Migration\Import\PagePathMapperInterface', 'XanUtility\Migration\Import\PagePathMapper');
    }
}
