<?php

namespace HetBonteHert\Module\MenuCard\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use HetBonteHert\Module\MenuCard\Entity\MenuCard;

/**
 * @extends ServiceEntityRepository<MenuCard>
 *
 * @method MenuCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuCard[]    findAll()
 * @method MenuCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuCard::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(MenuCard $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(MenuCard $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return MenuCard[] Returns an array of MenuCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MenuCard
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
