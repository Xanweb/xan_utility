<?php
namespace XanUtility\PageList\Fetcher;

use Concrete\Core\Page\Page;
use XanUtility\Helper\Page as PageHelper;

class BlockPropertyFetcher extends FetcherImpl
{
    /**
     * @param string $handle
     * @param callable $fetchCallback function(BlockController $bcController)
     */
    public function __construct($handle, callable $fetchCallback)
    {
        $this->handle = $handle;
        $this->fetchCallback = $fetchCallback;
    }

    public function fetch(Page $page)
    {
        $block = (new PageHelper($page))->getBlock($this->getHandle());
        if (is_object($block)) {

            return ($this->fetchCallback)($block);
        }
    }
}