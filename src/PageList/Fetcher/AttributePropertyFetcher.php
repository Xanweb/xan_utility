<?php
namespace XanUtility\PageList\Fetcher;

use Concrete\Core\Page\Page;

class AttributePropertyFetcher extends FetcherImpl
{
    /**
     * @param string $handle
     */
    public function __construct($handle)
    {
        $this->handle = $handle;
        $this->fetchCallback = function (Page $page) use ($handle) {
            return $page->getAttribute($handle);
        };
    }
}