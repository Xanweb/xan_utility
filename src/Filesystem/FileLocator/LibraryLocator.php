<?php
namespace XanUtility\Filesystem\FileLocator;

use Concrete\Core\Filesystem\FileLocator\AbstractLocation;

class LibraryLocator extends AbstractLocation
{
    const LIB_HANDLE = 'xan_utility';

    private $baseDir;

    public function __construct()
    {
        $handleLength = strlen(self::LIB_HANDLE);
        $dir = __DIR__;
        do {
            $dir = dirname($dir);
        } while ($dir && self::LIB_HANDLE != substr($dir, -$handleLength));
        $this->baseDir = $dir;
    }

    public function getCacheKey()
    {
        return ['vendor', 'xan-utility'];
    }

    public function getPath()
    {
        return $this->baseDir;
    }

    public function getURL()
    {
        $relativePath = str_replace(DIR_BASE, '', $this->baseDir);

        return DIR_REL . '/' . $relativePath;
    }
}
