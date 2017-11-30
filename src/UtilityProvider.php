<?php
namespace XanUtility;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class UtilityProvider extends ServiceProvider
{
    /**
     * @var Application
     */
    protected $app;

    public function register()
    {
        $register = [
            'excel/export' => Service\Excel\Export::class,
            'excel/import' => Service\Excel\Import::class,
        ];
        foreach ($register as $key => $value) {
            $this->app->bindIf($key, $value);
        }

        $singletons = [
        ];
        foreach ($singletons as $key => $value) {
            $this->app->bindIf($key, $value, true);
        }
    }
}
