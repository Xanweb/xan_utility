<?php
namespace XanUtility\Repository\Search;

use XanUtility\Listing\AbstractRepositoryList;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

class Pagination extends \Concrete\Core\Search\Pagination\Pagination
{
    /** @var AbstractRepositoryList */
    protected $list;

    public function __construct(AbstractRepositoryList $itemList, AdapterInterface $adapter)
    {
        $this->list = $itemList;

        return Pagerfanta::__construct($adapter);
    }

    public function getItemListObject()
    {
        return $this->list;
    }

    public function getCurrentPageResults()
    {
        $this->list->debugStart();
        $results = Pagerfanta::getCurrentPageResults();
        $this->list->debugStop();

        return $results;
    }
}
