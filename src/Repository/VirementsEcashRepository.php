<?php

namespace App\Repository;

use App\Entity\VirementsEcash;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VirementsEcash>
 *
 * @method VirementsEcash|null find($id, $lockMode = null, $lockVersion = null)
 * @method VirementsEcash|null findOneBy(array $criteria, array $orderBy = null)
 * @method VirementsEcash[]    findAll()
 * @method VirementsEcash[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VirementsEcashRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VirementsEcash::class);
    }

    public function add(VirementsEcash $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VirementsEcash $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VirementsEcash[] Returns an array of VirementsEcash objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VirementsEcash
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
