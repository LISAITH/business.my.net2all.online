<?php

namespace App\Repository;

use App\Entity\ActivationAbonnements;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivationAbonnements>
 *
 * @method ActivationAbonnements|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivationAbonnements|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivationAbonnements[]    findAll()
 * @method ActivationAbonnements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivationAbonnementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivationAbonnements::class);
    }

    public function add(ActivationAbonnements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ActivationAbonnements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return ActivationAbonnements[] Returns an array of ActivationAbonnements objects
    */
   public function findByPointVente($point_vente): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.point_vente = :point_vente')
           ->setParameter('point_vente', $point_vente)
           ->orderBy('a.id', 'ASC')
          
           ->getQuery()
           ->getResult()
       ;
   }


    /**
    * @return ActivationAbonnements[] Returns an array of ActivationAbonnements objects
    */
    public function findByEntrepriseUser(User $user): array
    {
        return $this->createQueryBuilder('a')
            ->join("a.enseigne","e")
            ->join("e.entreprise","ee")
            ->andWhere("ee.user =:user")
            ->setParameter('user', $user->getId())
            ->orderBy('a.id', 'ASC')
           
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?ActivationAbonnements
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
