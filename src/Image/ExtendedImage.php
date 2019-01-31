<?php
namespace XanUtility\Image;

use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Html\Image;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;

class ExtendedImage extends Image
{
    /**
     * @var File
     */
    protected $image;

    /**
     * @param File $f
     * @param null $usePictureTag
     */
    public function __construct(File $f = null, $usePictureTag = null)
    {
        if (!is_object($f)) {
            return false;
        }

        if (isset($usePictureTag)) {
            $this->usePictureTag = $usePictureTag;
        } else {
            $this->loadPictureSettingsFromTheme();
        }

        if ($this->usePictureTag && !isset($this->theme)) {
            $c = Page::getCurrentPage();
            $this->theme = $c->getCollectionThemeObject();
        }

        $this->image = $f;
    }

    /**
     * @return \HtmlObject\Image
     */

    public function getTag()
    {
        if (!$this->tag) {
            $baseSrc = $this->image->getRelativePath();
            if (!$baseSrc) {
                $baseSrc = $this->image->getURL();
            }

            if ($this->usePictureTag) {
                $fallbackSrc = $baseSrc;
                $sources = [];

                foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
                    $type = ThumbnailType::getByHandle($thumbnail);
                    if ($type != null) {
                        $src = $this->image->getThumbnailURL($type->getBaseVersion());
                        $sources[] = ['src' => $src, 'width' => $width];
                        if ($width == 0) {
                            $fallbackSrc = $src;
                        }
                    }
                }

                $this->tag = Picture::create($sources, $fallbackSrc);
            } else {
                // Return a simple image tag.
                $this->tag = \HtmlObject\Image::create($baseSrc);
                $this->tag->width((string) $this->image->getAttribute('width'));
                $this->tag->height((string) $this->image->getAttribute('height'));
            }
        }

        return $this->tag;
    }

    /**
     * @param string $ratio 4:3 16:9 16:10
     * @return Picture
     */
    public function getPicture($ratio)
    {
        $fallbackSrc = $this->image->getRelativePath();
        if (!$fallbackSrc) {
            $fallbackSrc = $this->image->getURL();
        }

        $sources = [];
        foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
            $type = ThumbnailType::getByHandle($thumbnail);
            if ($type != null) {
                $thumb = $this->fitAspectRatio($ratio, $width);
                $sources[] = ['src' => $thumb->src, 'width' => $width];
                if ($width == 0) {
                    $fallbackSrc = $thumb->src;
                }
            }
        }

        return Picture::create($sources, $fallbackSrc);
    }

    /**
     * @param string $type
     * @param string $ratio 4:3 16:9 16:10
     * @return \HtmlObject\Image
     */
    public function getImage($type, $ratio = '')
    {
        if (!empty($ratio)) {
            $typeObj = ThumbnailType::getByHandle($type);
            $thumb = $this->fitAspectRatio($ratio, $typeObj->getWidth());
            $path = $thumb->src;
        } else {
            $path = $this->image->getThumbnailURL($type);
        }

        $tag = \HtmlObject\Image::create($path);
        return $tag;
    }

    /**
     * @param int|null $maxWidth The maximum width of the thumbnail (may be empty if $crop is false and $maxHeight is specified)
     * @param int|null $maxHeight The maximum height of the thumbnail (may be empty if $crop is false and $maxWidth is specified)
     * @param bool $cropImage Fit to bounds?
     *
     * @return \HtmlObject\Image
     */
    public function getCustomImage($maxWidth, $maxHeight, $cropImage = true)
    {
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $im = $app->make('helper/image');

        $thumb = $im->getThumbnail($this->image, $maxWidth, $maxHeight, $cropImage);

        $tag = \HtmlObject\Image::create();
        $tag->src($thumb->src)->width($thumb->width)->height($thumb->height);

        return $tag;
    }

    /**
     *
     * @param string $ratio 4:3 16:9 16:10
     * @param int|null $globalMaxWidth The maximum width of the thumbnail (may be empty if $crop is false and $maxHeight is specified)
     * @param int|null $globalMaxHeight The maximum height of the thumbnail (may be empty if $crop is false and $maxWidth is specified)
     *
     * @return string Image path
     */
    protected function fitAspectRatio($ratio, $globalMaxWidth = false, $globalMaxHeight = false)
    {
        $fv = $this->image->getApprovedVersion();

        $thumb = new \stdClass;
        $thumb->src = $fv->getRelativePath()?:$fv->getURL();
        $thumb->width = $fv->getAttribute('width');
        $thumb->height = $fv->getAttribute('height');

        $aX = doubleval(explode(':', $ratio)[0]);
        $aY = doubleval(explode(':', $ratio)[1]);

        if (!$aX || empty($aX) || !$aY || empty($aY)) {
            return $thumb;
        }

        $app = Application::getFacadeApplication();
        $ih = $app->make('helper/image');
        $calculatedRatio = $aX / $aY;

        $width    = $thumb->width;
        $height   = $thumb->height;
        $imgRatio = $width / $height;

        if ($imgRatio != $calculatedRatio) {
            if ($imgRatio < $calculatedRatio) {
                $maxWidth  = $width;
                $maxHeight = $width / $calculatedRatio;
            } else {
                $maxWidth  = $calculatedRatio * $height;
                $maxHeight = $height;
            }

            if ($globalMaxWidth && $globalMaxWidth < $maxWidth) {
                $maxWidth  = $globalMaxWidth;
                $maxHeight = $maxWidth / $calculatedRatio;
            }

            if ($globalMaxHeight && $globalMaxHeight < $maxHeight) {
                $maxWidth  = $calculatedRatio * $globalMaxHeight;
                $maxHeight = $globalMaxHeight;
            }

            $thumb = $ih->getThumbnail($this->image, round($maxWidth), round($maxHeight), true);
        } elseif ($globalMaxWidth > 0 || $globalMaxHeight > 0) {
            $thumb = $ih->getThumbnail($this->image, $globalMaxWidth, $globalMaxHeight, false);
        }

        return $thumb;
    }
}
