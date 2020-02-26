<?php
namespace XanUtility\Filesystem\FileLocator;

use Concrete\Core\Filesystem\FileLocator\AbstractLocation;
use Symfony\Component\Routing\Generator\UrlGenerator;

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
        return '/' . ltrim(UrlGenerator::getRelativePath(DIR_BASE . '/', $this->getPath()), '/');
    }
}
