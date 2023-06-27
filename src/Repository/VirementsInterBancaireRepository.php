<?php

namespace App\Repository;

use App\Entity\VirementsInterBancaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VirementsInterBancaire>
 *
 * @method VirementsInterBancaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method VirementsInterBancaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method VirementsInterBancaire[]    findAll()
 * @method VirementsInterBancaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VirementsInterBancaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VirementsInterBancaire::class);
    }

    public function add(VirementsInterBancaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VirementsInterBancaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VirementsInterBancaire[] Returns an array of VirementsInterBancaire objects
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

//    public function findOneBySomeField($value): ?VirementsInterBancaire
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
