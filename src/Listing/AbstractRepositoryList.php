<?php
namespace XanUtility\Listing;

use Concrete\Core\Search\ItemList\ItemList as AbstractItemList;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use XanUtility\Repository\Search\Pagination;
use XanUtility\App;
use Database;
use Exception;

/**
 * @method Pagination getPagination()
 */
abstract class AbstractRepositoryList extends AbstractItemList
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $query;

    private $orderings;

    /**
     *  @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct()
    {
        $this->orderings = [];
        $this->em = App::em();
        $this->query = $this->getRepository()->createQueryBuilder('q');
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    final public function getQueryObject()
    {
        return $this->query;
    }

    abstract protected function getEntityClassName();

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->getEntityClassName());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    protected function createPaginationObject()
    {
        $adapter = new DoctrineORMAdapter($this->getQueryObject());
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    public function executeGetResults()
    {
        $qb = $this->getQueryObject();

        return $qb->getQuery()->getResult();
    }

    public function getResult($mixed)
    {
        return false;
    }

    final public function getResults()
    {
        $this->debugStart();
        $executeResults = $this->executeGetResults();
        $this->debugStop();

        return $executeResults;
    }

    public function getTotalResults()
    {
        return count($this->executeGetResults());
    }

    public function getOne()
    {
        $qb = $this->getQueryObject();

        return $qb->getQuery()->getFirstResult();
    }

    public function debugStart()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(new EchoSQLLogger());
        }
    }

    public function debugStop()
    {
        if ($this->isDebugged()) {
            Database::get()->getConfiguration()->setSQLLogger(null);
        }
    }

    protected function executeSortBy($column, $direction = 'asc')
    {
        if (in_array(strtolower($direction), ['asc', 'desc'])) {
            $this->orderings[$column] = $direction;
            $this->query->orderBy($column, $direction);
        } else {
            throw new Exception(t('Invalid SQL in order by'));
        }
    }

    protected function executeSanitizedSortBy($column, $direction = 'asc')
    {
        if (0 === preg_match('/[^0-9a-zA-Z\$\.\_\x{0080}-\x{ffff}]+/u', $column)) {
            $this->executeSortBy($column, $direction);
        } else {
            throw new Exception(t('Invalid SQL in order by'));
        }
    }

    public function getPage()
    {
        if ($this->itemsPerPage > 0) {
            $pagination = $this->getPagination();

            return $pagination->getCurrentPageResults();
        } else {
            return $this->getResults();
        }
    }

    /**
     * Specifies an ordering for the query results.
     *
     * @param string $column  the ordering column
     * @param string $direction the ordering direction
     */
    public function addSortBy($column, $direction = 'asc')
    {
        $this->executeSortBy($column, $direction);
    }

    public function filter($field, $value, $comparison = '=')
    {
        if (false == $field) {
            $this->query->andWhere($value); // ugh
        } else {
            $this->query->andWhere(implode(' ',
                    [
                $field, $comparison, $value,
            ]));
        }
    }
}
