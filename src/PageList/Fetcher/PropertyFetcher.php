<?php
namespace XanUtility\PageList\Fetcher;

use Concrete\Core\Page\Page;

interface PropertyFetcher
{
    /**
     * @return string
     */
    public function getHandle();

    public function fetch(Page $page);
}