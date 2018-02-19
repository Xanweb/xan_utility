<?php
namespace XanUtility;

use Concrete\Core\Application\Application;

trait ApplicationTrait
{
    /**
     * @param string $make [optional]
     *
     * @return Application|mixed
     */
    protected function app($make = null)
    {
        if (!$this->app) {
            $this->app = Facade::getFacadeApplication();
        }

        if (!is_null($make)) {
            return $this->app->make($make);
        }

        return $this->app;
    }
}
