<?php

namespace App\Repository;

use App\Entity\VirementsBancaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VirementsBancaires>
 *
 * @method VirementsBancaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method VirementsBancaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method VirementsBancaires[]    findAll()
 * @method VirementsBancaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VirementsBancairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VirementsBancaires::class);
    }

    public function add(VirementsBancaires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VirementsBancaires $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VirementsBancaires[] Returns an array of VirementsBancaires objects
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

//    public function findOneBySomeField($value): ?VirementsBancaires
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
