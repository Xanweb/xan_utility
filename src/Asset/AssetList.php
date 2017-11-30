<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\AssetList as CoreAssetList;

class AssetList extends CoreAssetList
{
    public function register($assetType, $assetHandle, $filename, $args = [], $pkg = false)
    {
        $class = '\\XanUtility\\Asset\\' . Object::camelcase($assetType) . 'Asset';
        if (!class_exists($class)) {
            return parent::register($assetType, $assetHandle, $filename, $args, $pkg);
        }

        $defaults = [
            'position' => false,
            'local' => true,
            'version' => false,
            'combine' => -1,
            'minify' => -1, // use the asset default
        ];
        // overwrite all the defaults with the arguments
        $args = array_merge($defaults, $args);

        $o = new $class($assetHandle);
        $o->register($filename, $args, $pkg);
        $this->registerAsset($o);

        return $o;
    }
}
