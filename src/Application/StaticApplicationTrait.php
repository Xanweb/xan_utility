<?php
namespace XanUtility\Application;

use Concrete\Core\Support\Facade\Application;

trait StaticApplicationTrait
{

    /**
     * @param string $make [optional]
     *
     * @return \Concrete\Core\Application\Application|object
     */
    protected static function app($make = null)
    {
        $app = Application::getFacadeApplication();

        if (!is_null($make)) {
            return $app->make($make);
        }

        return $app;
    }
}
