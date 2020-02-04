<?php
namespace XanUtility\Entity\Service;

interface ServiceInterface
{
    /**
     * Get full entity class name.
     *
     * @return string
     */
    public function getEntityClass();

    /**
     * Create New Entity instance.
     *
     * @return \XanUtility\Foundation\ConcreteObject Entity object
     */
    public function createEntity();

    /**
     * Finds all entities in the repository.
     *
     * @return \XanUtility\Foundation\ConcreteObject[] the entities
     */
    public function getList();

    /**
     * Finds all entities in the repository sorted by given fields.
     *
     * @param array $orderBy
     *
     * @return \XanUtility\Foundation\ConcreteObject[] the entities
     */
    public function getSortedList($orderBy = []);

    /**
     * Finds the entity by its primary key / identifier.
     *
     * @param mixed    $id          The identifier
     *
     * @return \XanUtility\Foundation\ConcreteObject|null The entity instance or NULL if the entity can not be found
     */
    public function getByID($id);

    /**
     * Create Entity.
     *
     * @param array $data
     *
     * @return \XanUtility\Foundation\ConcreteObject
     */
    public function create(array $data = []);

    /**
     * Update Entity.
     *
     * @param \XanUtility\Foundation\ConcreteObject $entity
     * @param array $data
     *
     * @return bool
     */
    public function update($entity, array $data = []);

    /**
     * @deprecated use update()
     * Save Entity Data.
     *
     * @param \XanUtility\Foundation\ConcreteObject $entity
     * @param array $data
     *
     * @return bool
     */
    public function saveData($entity, array $data = []);

    /**
     * Persist a list of entities and flush all changes.
     *
     * @param array $entities
     */
    public function bulkSave(array $entities);

    /**
     * Delete Entity.
     *
     * @param \XanUtility\Foundation\ConcreteObject $entity
     *
     * @return bool
     */
    public function delete($entity);

    /**
     * Delete a list of entities and flush all changes.
     *
     * @param array $entities
     */
    public function bulkDelete(array $entities);
}
