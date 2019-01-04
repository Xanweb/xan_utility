<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\CssAsset as CoreCssAsset;
use Concrete\Core\Filesystem\FileLocator;
use XanUtility\Filesystem\FileLocator\LibraryLocator;
use XanUtility\Application\ApplicationTrait;

class VendorCssAsset extends CoreCssAsset
{
    use ApplicationTrait;

    public function getAssetType()
    {
        return 'vendor-css';
    }

    public function getOutputAssetType()
    {
        return 'css';
    }

    public function mapAssetLocation($path)
    {
        if ($this->isAssetLocal()) {
            $locator = $this->app()->make(FileLocator::class);
            $locator->addLocation(new LibraryLocator());
            $r = $locator->getRecord($path);
            $this->setAssetPath($r->file);
            $this->setAssetURL($r->url);
        } else {
            $this->setAssetURL($path);
        }
    }
}
