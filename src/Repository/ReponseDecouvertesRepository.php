<?php

namespace App\Repository;

use App\Entity\ReponseDecouvertes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReponseDecouvertes>
 *
 * @method ReponseDecouvertes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponseDecouvertes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponseDecouvertes[]    findAll()
 * @method ReponseDecouvertes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponseDecouvertesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReponseDecouvertes::class);
    }

    public function add(ReponseDecouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReponseDecouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ReponseDecouvertes[] Returns an array of ReponseDecouvertes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ReponseDecouvertes
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
