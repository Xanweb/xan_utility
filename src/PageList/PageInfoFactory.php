<?php
namespace XanUtility\PageList;

use Concrete\Core\Page\Page;
use Concrete\Core\Application\Application;
use Concrete\Core\Url\Resolver\PageUrlResolver;

class PageInfoFactory
{
    /**
     * @var PageUrlResolver
     */
    private $urlResolver;

    /**
     * @var \Concrete\Core\Localization\Service\Date
     */
    private $dh;

    /**
     * @var \Concrete\Core\Utility\Service\Text
     */
    private $th;

    /**
     * @var PageInfoConfig
     */
    private $config;

    public function __construct(Application $app, PageUrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
        $this->dh = $app->make('date');
        $this->th = $app->make('helper/text');
        $this->config = $app->make('page_info/config/default');
    }

    /**
     * Build PageInfo Fetcher.
     *
     * @param Page $page
     * @param PageInfoConfig $config
     *
     * @return PageInfo|null Return PageInfo object or Null if page has COLLECTION_NOT_FOUND Error
     */
    public function build(Page $page, PageInfoConfig $config = null)
    {
        $pageInfo = null;
        if ($page->getError() !== COLLECTION_NOT_FOUND) {
            $pageInfo = new PageInfo($page, $this->urlResolver, $this->th, $this->dh, $config ?? $this->config);
        }

        return $pageInfo;
    }

    /**
     * Get Default Config
     *
     * @return PageInfoConfig
     */
    public function getConfig(): PageInfoConfig
    {
        return $this->config;
    }

    /**
     * Set Default Config
     *
     * @param PageInfoConfig $config
     */
    public function setConfig(PageInfoConfig $config)
    {
        $this->config = $config;
    }
}