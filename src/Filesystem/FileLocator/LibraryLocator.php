<?php
namespace XanUtility\Filesystem\FileLocator;

use Concrete\Core\Filesystem\FileLocator\AbstractLocation;

class LibraryLocator extends AbstractLocation
{
    const LIB_HANDLE = 'xanweb/xan_utility';

    private $baseDir;

    public function __construct()
    {
        $libDirName = last(explode('/', self::LIB_HANDLE));
        $handleLength = strlen($libDirName);
        $dir = __DIR__;
        do {
            $dir = dirname($dir);
        } while ($dir && $libDirName != substr($dir, -$handleLength));
        $this->baseDir = str_replace(DIRECTORY_SEPARATOR, '/', $dir);
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

        return DIR_REL . '/' . ltrim($relativePath, '/');
    }
}
