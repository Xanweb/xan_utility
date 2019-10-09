<?php defined('C5_EXECUTE') or die('Access Denied.');

use XanUtility\Helper\Str;

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

if (!function_exists('c5app')) {
    /**
     * Get the root Facade application instance.
     *
     * @param  string  $make
     *
     * @return \Concrete\Core\Application\Application|object
     */
    function c5app($make = null)
    {
        if (!is_null($make)) {
            return c5app()->make($make);
        }

        return \Concrete\Core\Support\Facade\Facade::getFacadeApplication();
    }
}

if (!function_exists('remove_accents')) {
    /**
     * Replace special chars with normal ones.
     *
     * @param  string  string with accents
     *
     * @return mixed
     */
    function remove_accents($string)
    {
        return Str::removeAccents($string);
    }
}

if (!function_exists('absolute_path')) {
    /**
     * Get absolute path from relative.
     *
     * @param  string $relPath  relative path
     *
     * @return string
     */
    function absolute_path($relPath)
    {
        if (is_absolute_path($relPath)) {
            return $relPath;
        }
        if (!starts_with($relPath, ['/', '\\'])) {
            $relPath = DIRECTORY_SEPARATOR . $relPath;
        }

        return DIR_BASE . $relPath;
    }
}

if (!function_exists('is_absolute_path')) {
    /**
     * Check if path is absolute.
     *
     * @param  string $path
     *
     * @return string
     */
    function is_absolute_path($path)
    {
        return strpos($path, DIR_BASE) !== false;
    }
}

if (!function_exists('get_theme_path')) {
    /**
     * Get theme relative path.
     *
     * @return string
     */
    function get_theme_path()
    {
        static $themePath;

        if (!$themePath) {
            $themePath = PageTheme::getSiteTheme()->getThemeURL();
        }

        return $themePath;
    }
}

if (!function_exists('get_active_language')) {
    /**
     * Get Active Site Language en|de...
     *
     * @return string
     */
    function get_active_language()
    {
        return current(explode('_', get_active_locale()));
    }
}

if (!function_exists('get_active_locale')) {
    /**
     * Get Active Site Locale en_US|de_DE...
     *
     * @return string
     */
    function get_active_locale()
    {
        $section = MultilingualSection::getCurrentSection();

        if (is_object($section)) {
            return $section->getLocale();
        }

        return Localization::activeLocale();
    }
}

if (!function_exists('getRandomItemByInterval')) {
    function getRandomItemByInterval($timeBase, $array)
    {
        // By using the modulus operator we get a pseudo
        // random index position that is between zero and the
        // maximal value (ItemsCount)
        $randomIndexPos = (((int) $timeBase) % count($array));

        return $array[$randomIndexPos];
    }
}

if (!function_exists('c5_date_format_custom')) {
    /**
     * An Alias of \Concrete\Core\Localization\Service\Date::formatCustom().
     *
     * Render a date/time as a localized string, by specifying a custom format.
     *
     * @param string $format The custom format (see http://www.php.net/manual/en/function.date.php for applicable formats)
     * @param mixed $value The date/time representation (one of the values accepted by toDateTime)
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     * @param string $fromTimezone The original timezone of $value (useful only if $value is a string like '2000-12-31 23:59'); it accepts the same values as $toTimezone
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    function c5_date_format_custom($format, $value = 'now', $toTimezone = 'user', $fromTimezone = 'system')
    {
        static $dh;
        if (!$dh) {
            $dh = c5app('date');
        }

        return $dh->formatCustom($format, $value, $toTimezone, $fromTimezone);
    }
}

if (!function_exists('c5_date_format')) {
    /**
     * An Alias of \Concrete\Core\Localization\Service\Date::formatDate().
     *
     * Render the date part of a date/time as a localized string.
     *
     * @param mixed $value $The date/time representation (one of the values accepted by toDateTime)
     * @param string $format the format name; it can be 'full' (eg 'EEEE, MMMM d, y' - 'Wednesday, August 20, 2014'), 'long' (eg 'MMMM d, y' - 'August 20, 2014'), 'medium' (eg 'MMM d, y' - 'August 20, 2014') or 'short' (eg 'M/d/yy' - '8/20/14'),
     *                      or a skeleton pattern prefixed by '~', e.g. '~yMd'.
     *                      You can also append a caret ('^') or an asterisk ('*') to $width. If so, special day names may be used (like 'Today', 'Yesterday', 'Tomorrow' with '^' and 'today', 'yesterday', 'tomorrow' width '*') instead of the date.
     * @param string $toTimezone The timezone to set. Special values are:<ul>
     *     <li>'system' for the current system timezone</li>
     *     <li>'user' (default) for the user's timezone</li>
     *     <li>'app' for the app's timezone</li>
     *     <li>Other values: one of the PHP supported time zones (see http://us1.php.net/manual/en/timezones.php )</li>
     * </ul>
     *
     * @return string Returns an empty string if $value couldn't be parsed, the localized string otherwise
     */
    function c5_date_format($value = 'now', $format = 'short', $toTimezone = 'user')
    {
        static $dh;
        if (!$dh) {
            $dh = c5app('date');
        }

        return $dh->formatDate($value, $format, $toTimezone);
    }
}

if (!function_exists('array_key_first')) {
    /**
     * Gets the first key of an array.
     *
     * @param array $array
     *
     * @return mixed
     */
    function array_key_first(array $array)
    {
        foreach ($array as $key => $unused) {
            return $key;
        }

        return null;
    }
}

if (!function_exists('array_key_last')) {
    /**
     * Gets the last key of an array.
     *
     * @param array $array
     *
     * @return mixed
     */
    function array_key_last(array $array)
    {
        return (!empty($array)) ? array_keys($array)[count($array) - 1] : null;
    }
}
