<?php
namespace XanUtility\Entity\Service;

use Doctrine\ORM\EntityManagerInterface;

abstract class EntityService implements ServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Gets the repository for the entity class.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function repo()
    {
        return $this->entityManager->getRepository($this->getEntityClass());
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::createEntity()
     */
    public function createEntity()
    {
        return c5app()->make($this->getEntityClass());
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::getList()
     */
    public function getList()
    {
        return $this->repo()->findAll();
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::getSortedList()
     */
    public function getSortedList($orderBy = [])
    {
        return $this->repo()->findBy([], $orderBy);
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::getByID()
     */
    public function getByID($id)
    {
        return $this->repo()->find($id);
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::saveData()
     */
    public function saveData($entity, array $data = [])
    {
        $entity->setPropertiesFromArray($data);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::bulkSave()
     */
    public function bulkSave(array $entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::delete()
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::bulkDelete()
     */
    public function bulkDelete(array $entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }
        $this->entityManager->flush();

        return true;
    }
}
