<?php

namespace App\Repository;

use App\Entity\JoinEnseigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JoinEnseigne>
 *
 * @method JoinEnseigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method JoinEnseigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method JoinEnseigne[]    findAll()
 * @method JoinEnseigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JoinEnseigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JoinEnseigne::class);
    }

    public function add(JoinEnseigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JoinEnseigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return JoinEnseigne[] Returns an array of JoinEnseigne objects
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

//    public function findOneBySomeField($value): ?JoinEnseigne
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
