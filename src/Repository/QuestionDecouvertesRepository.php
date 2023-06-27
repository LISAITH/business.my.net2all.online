<?php

namespace App\Repository;

use App\Entity\QuestionDecouvertes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionDecouvertes>
 *
 * @method QuestionDecouvertes|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionDecouvertes|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionDecouvertes[]    findAll()
 * @method QuestionDecouvertes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionDecouvertesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionDecouvertes::class);
    }

    public function add(QuestionDecouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(QuestionDecouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return QuestionDecouvertes[] Returns an array of QuestionDecouvertes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QuestionDecouvertes
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
