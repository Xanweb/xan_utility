<?php
namespace XanUtility\Asset;

use Concrete\Core\Asset\JavascriptAsset;

class JavascriptAsyncAsset extends JavascriptAsset
{
    /**
     * @var bool
     */
    protected $assetSupportsMinification = false;

    /**
     * @var bool
     */
    protected $assetSupportsCombination = false;

    /**
     * @return bool
     */
    public function isAssetLocal()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return 'javascript-async';
    }

    public function getOutputAssetType()
    {
        return 'javascript';
    }

    protected static function getOutputDirectory()
    {
        return false;
    }

    public function register($filename, $args, $pkg = false)
    {
        $defaults = [
            'position' => false,
            'local' => false,
            'version' => false,
            'combine' => -1,
            'minify' => -1, // use the asset default
        ];
        // overwrite all the defaults with the arguments
        $args = array_merge($defaults, $args);
        parent::register($filename, $args, $pkg);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '<script async defer src="' . $this->getAssetURL() . '"></script>';
    }
}
