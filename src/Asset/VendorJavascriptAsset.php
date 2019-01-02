<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\JavascriptAsset as CoreJavascriptAsset;
use Concrete\Core\Filesystem\FileLocator;
use XanUtility\Filesystem\FileLocator\LibraryLocator;
use XanUtility\Application\ApplicationTrait;

class VendorJavascriptAsset extends CoreJavascriptAsset
{
    use ApplicationTrait;

    public function getAssetType()
    {
        return 'vendor-javascript';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
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
