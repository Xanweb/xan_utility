<?php
namespace XanUtility\PageList;

use Concrete\Core\Attribute\Category\PageCategory;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use XanUtility\PageList\Fetcher\PropertyFetcher;

class PageInfoConfig
{
    /**
     * @var PageCategory
     */
    private $akc;

    /**
     * @var AttributeKey
     */
    private $akNavTarget;

    /**
     * @return PropertyFetcher[]
     */
    private $pageNameFetchers = [];

    /**
     * @return PropertyFetcher[]
     */
    private $pageDescriptionFetchers = [];

    /**
     * @return PropertyFetcher[]
     */
    private $thumbnailFetchers = [];

    public function __construct(PageCategory $akc)
    {
        $this->akc = $akc;
        $this->setNavTargetAttributeKey('nav_target');
    }

    /**
     * Get Nav Target Attribute Key.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key|null
     */
    public function getNavTargetAttributeKey()
    {
        return $this->akNavTarget;
    }

    /**
     * Set Nav Target Attribute Key.
     *
     * @param string $akHandle
     */
    public function setNavTargetAttributeKey($akHandle)
    {
        $this->akNavTarget = $this->akc->getAttributeKeyByHandle($akHandle);
    }

    /**
     * Register Page Name Fetcher (Call Order is important)
     *
     * @param PropertyFetcher $fetcher
     */
    public function registerPageNameFetcher(PropertyFetcher $fetcher)
    {
        $this->pageNameFetchers[] = $fetcher;
    }

    /**
     * Register Page Description Fetcher (Call Order is important)
     *
     * @param PropertyFetcher $fetcher
     */
    public function registerPageDescriptionFetcher(PropertyFetcher $fetcher)
    {
        $this->pageDescriptionFetchers[] = $fetcher;
    }

    /**
     * Register Thumbnail Fetcher (Call Order is important)
     *
     * @param PropertyFetcher $fetcher
     */
    public function registerThumbnailFetcher(PropertyFetcher $fetcher)
    {
        $this->thumbnailFetchers[] = $fetcher;
    }

    /**
     * @return PropertyFetcher[]
     */
    public function getPageDescriptionFetchers(): array
    {
        return $this->pageDescriptionFetchers;
    }

    /**
     * @return PropertyFetcher[]
     */
    public function getPageNameFetchers(): array
    {
        return $this->pageNameFetchers;
    }

    /**
     * @return PropertyFetcher[]
     */
    public function getThumbnailFetchers(): array
    {
        return $this->thumbnailFetchers;
    }
}