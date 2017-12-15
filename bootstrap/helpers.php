<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (!function_exists('mergeSiteConfig')) {
    /**
     * Merge the given config with config in site folder.
     *
     * @param array $config default config
     * @param string $file file name or path (__FILE__)
     *
     * @return array array of merged config
     */
    function mergeSiteConfig(array $config, $file)
    {
        $siteConfigFile = implode(DIRECTORY_SEPARATOR, [
        DIR_APPLICATION, DIRNAME_CONFIG, 'site', basename($file),
    ]);

        if (file_exists($siteConfigFile)) {
            $siteConfig = require $siteConfigFile;

            return array_replace_recursive($config, $siteConfig);
        }

        return $config;
    }
}

if (!function_exists('c5_app')) {
    /**
     * Get the root Facade application instance.
     *
     * @param  string  $make
     *
     * @return mixed
     */
    function c5_app($make = null)
    {
        if (!is_null($make)) {
            return c5_app()->make($make);
        }

        return \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
    }
}
