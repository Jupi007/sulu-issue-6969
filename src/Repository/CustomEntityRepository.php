<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CustomEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomEntity>
 *
 * @method CustomEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomEntity[] findAll()
 * @method CustomEntity[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomEntity::class);
    }

    public function create(): CustomEntity
    {
        return new CustomEntity();
    }

    public function save(CustomEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(int $id): void
    {
        /** @var object $location */
        $location = $this->getEntityManager()->getReference(
            $this->getClassName(),
            $id,
        );

        $this->getEntityManager()->remove($location);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?CustomEntity
    {
        return $this->find($id);
    }
}
