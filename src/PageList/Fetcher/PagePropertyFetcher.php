<?php
namespace XanUtility\PageList\Fetcher;

use Concrete\Core\Page\Page;

class PagePropertyFetcher extends FetcherImpl
{
    const PAGE_NAME = 'page_name';
    const PAGE_DESCRIPTION = 'page_description';

    /**
     * @param string $handle PAGE_NAME or PAGE_DESCRIPTION
     * @throws \Exception
     */
    public function __construct($handle)
    {
        $this->handle = $handle;
        switch ($handle) {
            case self::PAGE_NAME:
                $this->fetchCallback = function (Page $page) {
                    return $page->getCollectionName();
                };

                break;
            case self::PAGE_DESCRIPTION:
                $this->fetchCallback = function (Page $page) {
                    return $page->getCollectionDescription();
                };
                break;
            default:
                throw new \Exception(t('Unsupported Property'));
        }
    }
}