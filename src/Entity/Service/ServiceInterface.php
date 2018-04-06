<?php

namespace XanUtility\Entity\Service;


use XanUtility\Entity\EntityBase;

interface ServiceInterface
{
    /**
     * Get full entity class name
     * @return string
     */
    function getEntityClass();

    /**
     * Gets the repository for the entity class.
     * @return \Doctrine\ORM\EntityRepository
     */
    function repo();

    /**
     * Create New Entity instance
     * @return EntityBase Entity object
     */
    function newEntity();

    /**
     * Finds all entities in the repository.
     *
     * @return EntityBase[] The entities.
     */
    function getList();

    /**
     * Finds the entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier
     *
     * @return EntityBase|null The entity instance or NULL if the entity can not be found
     */
    function getByID($id);

    /**
     * Save Entity Data
     * @param EntityBase $entity
     * @param array $data
     * @return bool
     */
    function saveData(EntityBase $entity, array $data);

    /**
     * Delete Entity
     * @param EntityBase $entity
     * @return bool
     */
    function delete(EntityBase $entity);

}