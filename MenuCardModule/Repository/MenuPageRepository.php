<?php

namespace  HetBonteHert\Module\MenuCard\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HetBonteHert\Module\MenuCard\Entity\MenuPage;
use Tidi\Cms\Module\Core\Entity\Structure;

/**
 * @method MenuPage|null findOneByStructure(?Structure $structure)
 * @method MenuPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuPage[]    findAll()
 * @method MenuPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository<MenuPage>
 */
class MenuPageRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuPage::class);
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByStructureId($id)
    {
        return $this->createQueryBuilder('l')
            ->join('l.structure', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }
}
