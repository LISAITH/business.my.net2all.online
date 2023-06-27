<?php

namespace App\Repository;

use App\Entity\ApiCommunautes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiCommunautes>
 *
 * @method ApiCommunautes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiCommunautes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiCommunautes[]    findAll()
 * @method ApiCommunautes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiCommunautesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiCommunautes::class);
    }

    public function add(ApiCommunautes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiCommunautes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ApiCommunautes[] Returns an array of ApiCommunautes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ApiCommunautes
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
