<?php

namespace XanUtility\Entity\Service;

use Concrete\Core\Application\Application;
use XanUtility\Entity\EntityBase;
use XanUtility\App;


abstract class AbstractServiceImpl implements ServiceInterface
{
    /**
     * @var Application
     */
    protected $app;

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    function repo()
    {
        return App::em()->getRepository($this->getEntityClass());
    }

    /**
     * @inheritdoc
     */
    function newEntity()
    {
        return $this->app->make($this->getEntityClass());
    }

    /**
     * @inheritdoc
     */
    function getList()
    {
        return $this->repo()->findAll();
    }

    /**
     * @inheritdoc
     */
    function getByID($id)
    {
        return $this->repo()->find($id);
    }

    /**
     * @inheritdoc
     */
    function saveData(EntityBase $entity, array $data)
    {
        $entity->setPropertiesFromArray($data);
        $entity->save();

        return true;
    }

    /**
     * @inheritdoc
     */
    function delete(EntityBase $entity)
    {
        $entity->delete();

        return true;
    }

}