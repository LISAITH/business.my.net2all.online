<?php

namespace App\Repository;

use App\Entity\ProspectionPaiements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProspectionPaiements>
 *
 * @method ProspectionPaiements|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProspectionPaiements|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProspectionPaiements[]    findAll()
 * @method ProspectionPaiements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProspectionPaiementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProspectionPaiements::class);
    }

    public function add(ProspectionPaiements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProspectionPaiements $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return ProspectionPaiements[] Returns an array of ProspectionPaiements objects
    */
   public function findByAnneeMois($annee_mois): array
   {
       $results = $this->createQueryBuilder('p')
           ->andWhere('p.annee_mois = :annee_mois')
           ->setParameter('annee_mois', $annee_mois)
           ->orderBy('p.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
       $return=[];
       foreach($results as $result){
        $return[$result->getParticulier()->getId()]=$result;
       }
       
       return $return;
   }

//    public function findOneBySomeField($value): ?ProspectionPaiements
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
