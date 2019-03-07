<?php
namespace XanUtility\Form;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        $singletons = [
            'helper/form/page_selector' => '\XanUtility\Form\Service\Widget\PageSelector',
        ];

        foreach ($singletons as $key => $value) {
            $this->app->singleton($key, $value);
        }
    }
}
