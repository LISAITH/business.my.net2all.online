<?php

namespace App\Repository;

use App\Entity\ParticulierProfilProspections;
use App\Entity\Prospections;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Prospections>
 *
 * @method Prospections|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prospections|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prospections[]    findAll()
 * @method Prospections[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProspectionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prospections::class);
    }

    public function add(Prospections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Prospections $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Prospections[] Returns an array of Prospections objects
    */
   public function findByParentTree($particulier_id): array
   {    
       $return = $this->createQueryBuilder('p')
            ->innerJoin(
                'App\Entity\ParticulierProfilProspections',
                'pp',\Doctrine\ORM\Query\Expr\Join::WITH,
                'pp.particulier = p.particulier'
            )
            ->andWhere("pp.parent_parent LIKE :val")
            ->setParameter('val', "%".$particulier_id."%")
        
        
          
           ->getQuery()
           ->getResult()
       ;
               
        return $return;
   }

   /**
    * prendre les prospections par date et  par particulier
    * @return [] Returns an array of Prospections objects
    */
    public function findByPaginatedByDay($particulier_id): array
    {    
        $returns = $this->createQueryBuilder('p')
            ->addSelect("date(p.done_at) AS done")
             ->innerJoin(
                 'App\Entity\ParticulierProfilProspections',
                 'pp',\Doctrine\ORM\Query\Expr\Join::WITH,
                 'pp.particulier = p.particulier'
             )
             
            
             
             ->andWhere("pp.parent_parent LIKE :val")
             
             ->setParameter('val', "%".$particulier_id."%")
             ->groupBy("p.id"," done")
             ->orderBy("p.done_at","desc")
            ->getQuery()
            ->getResult()
        ;

        $rangePerDate =[];

        foreach($returns as $return){
          if(!key_exists($return["done"],$rangePerDate))  $rangePerDate[$return["done"]]=[];
            array_push($rangePerDate[$return["done"]],$return[0]);

        }


                
         return $rangePerDate;
    }


    /**
    * prendre les prospections par date et  par particulier
    * @return [] Returns an array of Prospections objects
    */
    public function findAcitveMonth(): array
    {    
        $results = $this->createQueryBuilder('p')
            ->select("yearmonth(p.done_at) AS done,p.done_at AS ref_date")
            
             ->groupBy("done")
             ->orderBy("done","desc")
            ->getQuery()
            ->getResult()
        ;

        $return=[];

        foreach($results as $result){
            $return[$result['done']]=$result['ref_date'];
        }

                
        return $return;
    }





    /**
    * @return [] Returns an array of Prospections objects
    */
    public function findProsDay($prosPerDay): array
    {   
        $return=[]; 
        $returns = $this->createQueryBuilder('p')
            ->select("count(p.id) as total_day ,IDENTITY(p.particulier) as author")
            ->where("p.done_at > :day")
            ->groupBy("p.particulier")
            ->setParameter("day" ,Date("Y-m-d"))
            ->getQuery()
            ->getResult()
        ;
        
        foreach($returns as $item){
            
            $return[$item["author"]]=( int)$item["total_day"]*100/($prosPerDay!=0 ?$prosPerDay:1);
        }

       
         return $return;
    }


    /**
    * @return [] Returns an array of Prospections objects
    */
    public function findProsMensuel($prosPerMounth,$date): array
    {   
        $return=[]; 
        $returns = $this->createQueryBuilder('p')
            ->select("count(p.id) as total_mounth ,IDENTITY(p.particulier) as author")
            ->where("year(p.done_at) = year(:day)","yearmonth(p.done_at) = yearmonth(:day)")
            ->groupBy("p.particulier")
            ->setParameter("day" ,$date)
            ->getQuery()
            ->getResult()
        ;
       


      
        foreach($returns as $item){
          
            $return[$item["author"]]=$item["total_mounth"]*100/$prosPerMounth;
        }

   
         return $return;
    }

        /**
    * @return int Returns an array of Prospections objects
    */
    public function findByOneDay($particulier_id): int
    {   
      
        $returns = $this->createQueryBuilder('p')
            ->select("count(p.id) as total_day")
            ->andWhere("date(p.done_at) = date(:day)")
            ->andWhere("p.particulier =:particulier_id")
            ->setParameter("day" ,Date("Y-m-d"))
            ->setParameter('particulier_id', $particulier_id)
            ->getQuery()
            ->getResult()
        ;
        
     
          
       
         return $returns[0]["total_day"];
    }


          /**
    * @return int Returns an array of Prospections objects
    */
    public function findByOneWeek($particulier_id): int
    {   
      
        $returns = $this->createQueryBuilder('p')
            ->select("count(p.id) as total_week")
            ->andWhere("year(p.done_at) = year(:day)")
            ->andWhere("month(p.done_at) = month(:day)")
            ->andWhere("week(p.done_at,1) = week(:day,1)")
            ->andWhere("p.particulier =:particulier_id")
            ->setParameter("day" ,Date("Y-m-d"))
            ->setParameter('particulier_id', $particulier_id)
            ->getQuery()
            ->getResult()

        ;
        
       
          
       
         return $returns[0]["total_week"];
    }


           /**
    * @return int Returns an array of Prospections objects
    */
    public function findByOneMounth($particulier_id): int
    {   
      
        $returns = $this->createQueryBuilder('p')
            ->select("count(p.id) as total_mounth")
            ->andWhere("year(p.done_at) = year(:day)")
            ->andWhere("yearmonth(p.done_at) = yearmonth(:day)")
            ->andWhere("p.particulier =:particulier_id")
            ->setParameter("day" ,Date("Y-m-d"))
            ->setParameter('particulier_id', $particulier_id)
            ->getQuery()
            ->getResult()
        ;
        
     
          
        
         return $returns[0]["total_mounth"];
    }

   /**
    * @return Prospections[] Returns an array of Prospections objects
    */
    public function findByDayProsToValid($particulier_id,$date): array
    {   
      
        $returns = $this->createQueryBuilder('p')
            ->where("date(p.done_at) = date(:day)")
            ->andWhere("p.particulier =:particulier_id")
            ->setParameter("day" ,new DateTime($date))
            ->setParameter('particulier_id', $particulier_id)
            ->getQuery()
            ->getResult()
        ;
        
     
          
       
        return $returns;
    }


    // public function get1days(){
    //     // Given a date in string format 
    //         $datestring = '2020-04-23';
            
    //         // Converting string to date
    //         $date = strtotime($datestring);
            
    //         // Last date of current month.
    //         $lastdate = strtotime(date("Y-m-t", $date ));
            
            
    //         // Day of the last date 
    //         $day = date("l", $lastdate);
            
    //         echo $day;
    // }


    public function getDays($datestring )
    {
        // Given a date in string format 
        // $datestring 

        // Converting string to date
        $date = strtotime($datestring);

        // Last date of current month.
        $basedate = date("Y-m-", $date);
        $days = date("t", $date);
        $n=0;
        for($i=1;$i<=$days;$i++){

            $day_name =strtolower(date("l",strtotime($basedate.$i)));
            if($day_name!="sunday" && $day_name!="saturday"){
                $n++;
            }
        }


        // Day of the last date 
        // $day = date("l", $lastdate);

        return $n;
    }

//    public function findOneBySomeField($value): ?Prospections
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
