<?php

namespace App\Repository;

use App\Entity\PointVente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PointVente>
 *
 * @method PointVente|null find($id, $lockMode = null, $lockVersion = null)
 * @method PointVente|null findOneBy(array $criteria, array $orderBy = null)
 * @method PointVente[]    findAll()
 * @method PointVente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointVenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PointVente::class);
    }

    public function add(PointVente $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PointVente $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }




     /**
    * @return PointVente[] Returns an array of Abonnement objects
    */
   public function findByDistributeurField($distributeur): array
   {
       return $this->createQueryBuilder('p')
      
           ->andWhere('p.distributeur = :distributeur')
           ->setParameter('distributeur', $distributeur )
           ->orderBy('p.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }



   /**
    * @return PointVente Returns an array of Abonnement objects
    */
    public function findOneByDistributeurField($id,$distributeur)
    {
        return $this->createQueryBuilder('p')
          
            ->andWhere('p.distributeur = :distributeur')
            ->andWhere('p.id = :id')
            ->setParameter('distributeur', $distributeur )
            ->setParameter("id",$id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


//    /**
//     * @return PointVente[] Returns an array of PointVente objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PointVente
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
