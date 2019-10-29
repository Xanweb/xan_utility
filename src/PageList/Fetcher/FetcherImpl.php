<?php
namespace XanUtility\PageList\Fetcher;

use Concrete\Core\Page\Page;

abstract class FetcherImpl implements PropertyFetcher
{
    /**
     * @var string
     */
    protected $handle;

    /**
     * @var callable
     */
    protected $fetchCallback;

    /**
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    public function fetch(Page $page)
    {
        return ($this->fetchCallback)($page);
    }
}