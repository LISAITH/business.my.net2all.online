<?php

namespace App\Repository;

use App\Entity\Abonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonnement>
 *
 * @method Abonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnement[]    findAll()
 * @method Abonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

    public function add(Abonnement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Abonnement $entity, bool $flush = false): void
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
           ->join("a.distributeur","d")
           ->andWhere('d.partenaire = :partenaire_id')
           ->setParameter('partenaire_id', $partenaire_id)
           ->orderBy('a.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }


      /**
    * @return Abonnement[] Returns an array of Abonnement objects
    */
    public function findBydistributeurField($distributeur_id): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.distributeur = :distributeur_id')
            ->setParameter('distributeur_id', $distributeur_id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
    * @return Abonnement[] Returns an array of Abonnement objects
    */
    public function findByavalable($distributeur_id,$n): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.distributeur = :distributeur_id')
            ->andWhere('a.status = :status')
            ->andWhere('a.point_vente is NULL')
            ->setParameter('distributeur_id', $distributeur_id)
            ->setParameter('status', 1)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }


     /**
    * @return Abonnement[] Returns an array of Abonnement objects
    */
    public function findByavalableForPoint($point_vente): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->andWhere('a.point_vente  =:point_vente')
            ->setParameter('point_vente', $point_vente)
            ->setParameter('status', 1)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

       /**
    * @return Abonnement[] Returns an array of Abonnement objects
    */
    public function findByavalableForEnseigne(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
          
            ->setParameter('status', 1)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


       /**
    * @return Abonnement[] Returns an array of Abonnement objects
    */
    public function findKitsAvalable(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.activationAbonnements","aa")
            ->andWhere('a.status = :status')
            ->andWhere('aa.abonnement is NULL')
            ->andWhere('a.distributeur is NULL')
            ->setParameter('status', 1)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
         /**
    * @return Abonnement Returns an array of Abonnement objects
    */
    public function findOneKitAvalable(): Null|Abonnement
    {
        return $this->createQueryBuilder('a')
            ->leftJoin("a.activationAbonnements","aa")
            ->andWhere('a.status = :status')
            ->andWhere('aa.abonnement is NULL')
            ->andWhere('a.distributeur is NULL')
            ->setParameter('status', 1)
            ->setMaxResults(1)
            ->getQuery()
            
            ->getOneOrNullResult()
        ;
    }


    








   /**
    * @return Abonnement Returns an array of Abonnement objects
    */
    public function findOneByPartenaireField($id,$partenaire_id)
    {
        return $this->createQueryBuilder('a')
            ->join("a.distributeur","d")
            ->andWhere('d.partenaire = :partenaire_id')
            ->andWhere('a.id =:id')
            ->setParameter('partenaire_id', $partenaire_id)
            ->setParameter("id",$id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


    


//    /**
//     * @return Abonnement[] Returns an array of Abonnement objects
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

//    public function findOneBySomeField($value): ?Abonnement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
