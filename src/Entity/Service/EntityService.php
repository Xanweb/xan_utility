<?php
namespace XanUtility\Entity\Service;

use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Support\Facade\Application;

abstract class EntityService implements ServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;

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
        if (!$this->repo) {
            $this->repo = $this->entityManager->getRepository($this->getEntityClass());
        }

        return $this->repo;
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::createEntity()
     */
    public function createEntity()
    {
        $app = Application::getFacadeApplication();
        return $app->make($this->getEntityClass());
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
     * @see ServiceInterface::create()
     */
    public function create(array $data = [])
    {
        $entity = $this->createEntity();
        $entity->setPropertiesFromArray($data);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * {@inheritdoc}
     *
     * @see ServiceInterface::update()
     */
    public function update($entity, array $data = [])
    {
        $entity->setPropertiesFromArray($data);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @deprecated
     *
     * {@inheritdoc}
     *
     * @see ServiceInterface::saveData()
     */
    public function saveData($entity, array $data = [])
    {
        return $this->update($entity, $data);
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
