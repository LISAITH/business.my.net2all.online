<?php

namespace App\Repository;

use App\Entity\JoinEnseigneCommunaute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JoinEnseigneCommunaute>
 *
 * @method JoinEnseigneCommunaute|null find($id, $lockMode = null, $lockVersion = null)
 * @method JoinEnseigneCommunaute|null findOneBy(array $criteria, array $orderBy = null)
 * @method JoinEnseigneCommunaute[]    findAll()
 * @method JoinEnseigneCommunaute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoinEnseigneCommunauteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JoinEnseigneCommunaute::class);
    }

    public function add(JoinEnseigneCommunaute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JoinEnseigneCommunaute $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return JoinEnseigneCommunaute[] Returns an array of JoinEnseigneCommunaute objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JoinEnseigneCommunaute
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
