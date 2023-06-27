<?php

namespace App\Repository;

use App\Entity\SouscriptionFormules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SouscriptionFormules>
 *
 * @method SouscriptionFormules|null find($id, $lockMode = null, $lockVersion = null)
 * @method SouscriptionFormules|null findOneBy(array $criteria, array $orderBy = null)
 * @method SouscriptionFormules[]    findAll()
 * @method SouscriptionFormules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SouscriptionFormulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SouscriptionFormules::class);
    }

    public function add(SouscriptionFormules $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SouscriptionFormules $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }




    /**
     * @return Abonnement[] Returns an array of Abonnement objects
     */
    public function findByPartenaireField($partenaire_id): array
    {
        return $this->createQueryBuilder('a')
            ->join("a.distributeur", "d")
            ->andWhere('d.partenaire_user = :partenaire_id')
            ->setParameter('partenaire_id', $partenaire_id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return SouscriptionFormules[] Returns an array of SouscriptionFormules objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SouscriptionFormules
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}