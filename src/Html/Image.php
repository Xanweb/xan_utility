<?php
namespace XanUtility\Html;

use HtmlObject\Image as HtmlObjectImage;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use PageTheme;

class Image
{
    /**
     * @var \Concrete\Core\Page\Theme\Theme
     */
    protected $theme;

    /**
     * @var \Concrete\Core\Entity\Attribute\Key\Key
     */
    protected $altAttrKey;

    /**
     * Set the default Fallback image.
     *
     * @var string
     */
    protected $defaultFallbackImage = '';

    public function __construct(FileCategory $fakc)
    {
        $this->altAttrKey = $fakc->getAttributeKeyByHandle('alt');
        $this->loadTheme();
    }

    /**
     * Display Responsive Picture.
     *
     * @param File $img
     * @param string $fallbackImagePath
     */
    public function renderPicture($img, $fallbackImagePath = null)
    {
        echo $this->getPicture($img, $fallbackImagePath);
    }

    /**
     * @param File $img
     * @param string $fallbackImagePath
     *
     * @return Picture
     */
    public function getPicture($img, $fallbackImagePath = null)
    {
        $sources = [];
        $altText = '';
        $baseImageUrl = '';
        if (is_object($img)) {
            $fv = $img->getApprovedVersion();
            $altText = $this->getAltText($fv);

            $baseImageUrl = $fv->getRelativePath();
            if (!$baseImageUrl) {
                $baseImageUrl = $fv->getURL();
            }

            if (is_object($this->theme)) {
                foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
                    $type = ThumbnailType::getByHandle($thumbnail);
                    if ($type != null) {
                        $imageUrl = $fv->getThumbnailURL($type->getBaseVersion());
                        $imageUrl_2x = $fv->getThumbnailURL($type->getDoubledVersion());
                        if (!file_exists(absolute_path($imageUrl))) {
                            $imageUrl = $baseImageUrl;
                        }

                        $src = $imageUrl;
                        if (!empty($imageUrl_2x) && file_exists(absolute_path($imageUrl_2x))) {
                            $src = "$imageUrl_2x 2x, $imageUrl 1x";
                        }

                        $sources[] = ['src' => $src, 'width' => $width];
                    }
                }
            }
        }

        $picture = Picture::create($sources, !empty($baseImageUrl) ? $baseImageUrl : ($fallbackImagePath ?: $this->defaultFallbackImage));
        $picture->alt($altText);

        return $picture;
    }

    /**
     * Display Responsive Picture from configured theme thumbnails.
     *
     * @param File $img
     * @param string $fallbackImagePath
     */
    public function renderThumbnailsPicture($img, $fallbackImagePath = null)
    {
        echo $this->getThumbnailsPicture($img, $fallbackImagePath);
    }

    /**
     * @param File $img
     * @param string $fallbackImagePath
     *
     * @return Picture
     */
    public function getThumbnailsPicture($img, $fallbackImagePath = null)
    {
        $sources = [];
        $altText = '';
        $baseImageUrl = '';
        if (is_object($img)) {
            $fv = $img->getApprovedVersion();
            $altText = $this->getAltText($fv);

            $baseImageUrl = $fv->getRelativePath();
            if (!$baseImageUrl) {
                $baseImageUrl = $fv->getURL();
            }

            if (is_object($this->theme) && method_exists($this->theme, 'getThemeResponsiveImageThumbnailsMap')) {
                foreach ($this->theme->getThemeResponsiveImageThumbnailsMap() as $thumbnail => $width) {
                    $type = ThumbnailType::getByHandle($thumbnail);
                    if ($type != null) {
                        $imageUrl = $fv->getThumbnailURL($type->getBaseVersion());
                        $imageUrl_2x = $fv->getThumbnailURL($type->getDoubledVersion());
                        if (!file_exists(absolute_path($imageUrl))) {
                            $imageUrl = $baseImageUrl;
                        }

                        $src = $imageUrl;
                        if (!empty($imageUrl_2x) && file_exists(absolute_path($imageUrl_2x))) {
                            $src = "$imageUrl_2x 2x, $imageUrl 1x";
                        }

                        $sources[] = ['src' => $src, 'width' => $width];
                    }
                }
            }
        }

        $picture = Picture::create($sources, !empty($baseImageUrl) ? $baseImageUrl : ($fallbackImagePath ?: $this->defaultFallbackImage));
        $picture->alt($altText);

        return $picture;
    }

    /**
     * Display Responsive Image.
     *
     * @param $img
     */
    public function renderResponsiveImage($img)
    {
        echo $this->getResponsiveImage($img);
    }

    /**
     * @param File $img
     *
     * @return HtmlObjectImage
     */
    public function getResponsiveImage($img)
    {
        $thumbs = [];
        $srcSets = [];
        $sizes = [];
        $altText = '';
        $baseImageUrl = '';
        if (is_object($img)) {
            $fv = $img->getApprovedVersion();
            $altText = $this->getAltText($fv);

            $baseImageUrl = $fv->getRelativePath();
            if (!$baseImageUrl) {
                $baseImageUrl = $fv->getURL();
            }

            if ($fv->getExtension() != 'svg' && is_object($this->theme)) {
                foreach ($this->theme->getThemeResponsiveImageMap() as $thumbnail => $width) {
                    $type = ThumbnailType::getByHandle($thumbnail);
                    if ($type != null) {
                        $imageUrl = $fv->getThumbnailURL($type->getBaseVersion());
                        if (!file_exists(DIR_BASE . $imageUrl)) {
                            $imageUrl = $baseImageUrl;
                        }

                        $thumbs[] = $imageUrl;
                        $srcSets[] = "$imageUrl {$type->getWidth()}w";
                        $sizes[] = "(min-width: $width) {$type->getWidth()}px";
                    }
                }

                if (!empty($thumbs)) {
                    $baseImageUrl = head($thumbs);
                }
            }
        }

        $htmlImgTag = HtmlObjectImage::create($baseImageUrl, $altText);
        if (!empty($srcSets)) {
            $htmlImgTag->srcset(implode(', ', $srcSets));
            $htmlImgTag->sizes(implode(', ', $sizes));
        }

        return $htmlImgTag;
    }

    /**
     * @param File $img
     * @param string $thumbnail Thumbnail Handle
     */
    public function renderThumbnail($img, string $thumbnail)
    {
        echo $this->getThumbnailTag($img, $thumbnail);
    }

    /**
     * @param File $img
     * @param string $thumbnail
     *
     * @return HtmlObjectImage
     */
    public function getThumbnailTag($img, string $thumbnail)
    {
        $altText = '';
        $imageUrl = '';
        $attributes = [];

        if (is_object($img)) {
            $fv = $img->getApprovedVersion();
            $altText = $this->getAltText($fv);

            $imageSourceUrl = $fv->getRelativePath();
            if (!$imageSourceUrl) {
                $imageSourceUrl = $fv->getURL();
            }

            if ($fv->getExtension() === 'svg') {
                $imageUrl = $imageSourceUrl;
            } else {
                $type = ThumbnailType::getByHandle($thumbnail);
                $imageUrl = $fv->getThumbnailURL($type->getBaseVersion());
                $imageUrl_2x = $fv->getThumbnailURL($type->getDoubledVersion());

                if (!file_exists(absolute_path($imageUrl))) {
                    $imageUrl = $imageSourceUrl;
                }

                if (!empty($imageUrl_2x) && file_exists(absolute_path($imageUrl_2x))) {
                    $attributes['srcset'] = "$imageUrl_2x 2x, $imageUrl 1x";
                }
            }
        }

        return HtmlObjectImage::create(!empty($imageUrl) ? $imageUrl : $this->defaultFallbackImage, $altText, $attributes);
    }

    /**
     * Get Alt Text from File.
     *
     * @param \Concrete\Core\Entity\File\Version $fv
     *
     * @return string
     */
    private function getAltText($fv)
    {
        if (is_object($this->altAttrKey)) {
            return (string) $fv->getAttribute($this->altAttrKey, 'display');
        }

        return $fv->getTitle();
    }

    /**
     * Load theme from current page.
     */
    protected function loadTheme()
    {
        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $pt = $c->getPageController()->getTheme();
            if (is_object($pt)) {
                $pt = $pt->getThemeHandle();
            }

            $th = PageTheme::getByHandle($pt);
            if (is_object($th)) {
                $this->theme = $th;
            } else {
                $this->theme = PageTheme::getSiteTheme();
            }
        }
    }

    /**
     * Set the default Fallback image.
     *
     * @param string $defaultFallbackImage
     */
    public function setDefaultFallbackImage($defaultFallbackImage)
    {
        $this->defaultFallbackImage = $defaultFallbackImage;
    }
}
