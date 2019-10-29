<?php
namespace XanUtility\PageList;

use Concrete\Core\Page\Page;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Url\Resolver\PageUrlResolver;
use Concrete\Core\Utility\Service\Text;

class PageInfo
{
    /**
     * @var Page
     */
    private $page;

    /**
     * @var PageUrlResolver
     */
    private $urlResolver;

    /**
     * @var Text
     */
    private $th;

    /**
     * @var Date
     */
    private $dh;

    /**
     * @var PageInfoConfig
     */
    private $config;

    public function __construct(Page $page, PageUrlResolver $urlResolver, Text $th, Date $dh, PageInfoConfig $config)
    {
        $this->page = $page;
        $this->th = $th;
        $this->dh = $dh;
        $this->urlResolver = $urlResolver;
        $this->config = $config;
    }

    /**
     * Get Page Name after applying htmlentites().
     *
     * @return string
     */
    public function fetchPageName()
    {
        $pageName = '';
        foreach ($this->config->getPageNameFetchers() as $fetcher) {
            $pageName = $fetcher->fetch($this->page);
            if (!empty($pageName)) {
                break;
            }
        }

        return $this->th->entities($pageName);
    }

    /**
     * Get Page Description.
     *
     * @param int|null $truncateChars
     *
     * @return string
     */
    public function fetchPageDescription($truncateChars = null)
    {
        $description = '';
        foreach ($this->config->getPageDescriptionFetchers() as $fetcher) {
            $description = $fetcher->fetch($this->page);
            if (!empty($description)) {
                break;
            }
        }

        return $truncateChars ? $this->th->shortenTextWord($description, $truncateChars) : $description;
    }

    /**
     * Get Page URL.
     *
     * @return \League\URL\URLInterface
     */
    public function getURL()
    {
        return $this->urlResolver->resolve([$this->page]);
    }

    /**
     * Get Navigation Target.
     *
     * @return string
     */
    public function getTarget()
    {
        $akNavTarget = $this->config->getNavTargetAttributeKey();
        $target = ($this->page->getCollectionPointerExternalLink() != '' && $this->page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $this->page->getAttribute($akNavTarget);

        return empty($target) ? '_self' : $target;
    }

    /**
     * Get Publish Date.
     *
     * @param string|null $format The custom format (see http://www.php.net/manual/en/function.date.php for applicable formats)
     * @return string
     */
    public function getPublishDate($format = null)
    {
        $datePublic = $this->page->getCollectionDatePublic();
        return $format ? $this->dh->formatCustom($format, $datePublic) : $this->dh->formatDate($datePublic);
    }

    /**
     * Get Main Page Thumbnail.
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function fetchThumbnail()
    {
        $thumbnail = null;
        foreach ($this->config->getThumbnailFetchers() as $fetcher) {
            $thumbnail = $fetcher->fetch($this->page);
            if (is_object($thumbnail)) {
                break;
            }
        }

        return $thumbnail;
    }
}