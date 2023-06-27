<?php

namespace App\Repository;

use App\Entity\Particuliers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Particuliers>
 *
 * @method Particuliers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Particuliers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Particuliers[]    findAll()
 * @method Particuliers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticuliersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Particuliers::class);
    }

    public function add(Particuliers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Particuliers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Particuliers[] Returns an array of Particuliers objects
    */
   public function findByProspectAdmin(): array
   {
       return $this->createQueryBuilder('p')
            ->innerJoin("p.particulierProfilProspections","pp")
           ->andWhere('pp.profil_prospection = 5')
         
           ->orderBy('pp.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

     /**
    * @return Particuliers[] Returns an array of Particuliers objects
    */
    public function findByProspectManager($parent_id ): array
    {
        return $this->createQueryBuilder('p')
             ->innerJoin("p.particulierProfilProspections","pp")
            ->andWhere('pp.profil_prospection !=5')
            ->andWhere('pp.parent =:parent_id')
            ->setParameter('parent_id', $parent_id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

        /**
    * @return Particuliers[] Returns an array of Particuliers objects
    */
    public function findByProspectCommercial($parent_id ): array
    {
        return $this->createQueryBuilder('p')
             ->innerJoin("p.particulierProfilProspections","pp")
            ->andWhere('pp.profil_prospection =1')
            ->andWhere('pp.parent =:parent_id')
            ->setParameter('parent_id', $parent_id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Particuliers Returns an array of Particuliers objects
    */
    public function findByOneProspectCommercial($parent_id,$commercial_id ): ?Particuliers
    {
        return $this->createQueryBuilder('p')
             ->innerJoin("p.particulierProfilProspections","pp")
            ->andWhere('pp.profil_prospection =1')
            ->andWhere('pp.parent =:parent_id')
            ->andWhere('p.id =:commercial_id')
            ->setParameter('parent_id', $parent_id)
            ->setParameter('commercial_id', $commercial_id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    public function findOneBySomeField($value): ?Particuliers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
