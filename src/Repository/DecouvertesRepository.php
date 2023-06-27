<?php

namespace App\Repository;

use App\Entity\Decouvertes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Decouvertes>
 *
 * @method Decouvertes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Decouvertes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Decouvertes[]    findAll()
 * @method Decouvertes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DecouvertesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Decouvertes::class);
    }

    public function add(Decouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Decouvertes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return [] Returns an array of Decouvertes objects
    */
   public function findArray($prospection_id): array
   {   
        $tab=[];
        $returns = $this->createQueryBuilder('d')
           
           ->andWhere('d.prospection = :prospection')
           ->setParameter('prospection', $prospection_id)
          
           ->getQuery()
           ->getResult();
       ;

       foreach($returns as $return ){
            $tab[]=$return->getReponse()->getId();
       }

       return $tab;

   }

   



//    public function findOneBySomeField($value): ?Decouvertes
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
