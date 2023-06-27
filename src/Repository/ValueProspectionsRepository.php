<?php

namespace App\Repository;

use DateTime;
use App\Entity\ValueProspections;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ValueProspections>
 *
 * @method ValueProspections|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValueProspections|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValueProspections[]    findAll()
 * @method ValueProspections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValueProspectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValueProspections::class);
    }

    public function add(ValueProspections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ValueProspections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ValueProspections[] Returns an array of ValueProspections objects
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

   public function valueBy($param): ?string
   {
        $return = $this->createQueryBuilder('v')
            ->innerJoin("v.param_prospection","p")
   
            ->andWhere('v.done_at < :start_date')
            ->andWhere('p.code = :param')
           ->setParameter('start_date', new DateTime())
           ->setParameter('param', $param)
           ->getQuery()
           ->getOneOrNullResult()
       ;
       return  $return?$return->getValue():0;
   }
}
