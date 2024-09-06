<?php

namespace App\Repository;

use App\Entity\Video;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Video>
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    //    /**
    //     * @return Video[] Returns an array of Video objects
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

    //    public function findOneBySomeField($value): ?Video
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

         //methode permettant de trouver une video dans la barre de recherche
         public function findBySearch(SearchData $searchData): QueryBuilder
         {
             $queryBuilder = $this->createQueryBuilder('v');
         
             if ($searchData->query) {
                 $queryBuilder->andWhere('v.title LIKE :query')
                              ->setParameter('query', '%'.$searchData->query.'%');
             }
         
             return $queryBuilder;
         }
         

        // public function findBySearch(SearchData $searchData): QueryBuilder
        // {
        //     $queryBuilder = $this->createQueryBuilder('v');
    
        //     if ($searchData->query) {
        //         $queryBuilder->andWhere('v.title LIKE :query')
        //                      ->setParameter('query', '%'.$searchData->query.'%');
        //     }
    
        //     if ($searchData->isPremium) {
        //         $queryBuilder->andWhere('v.isPremium = :isPremium')
        //                      ->setParameter('isPremium', $searchData->isPremium);
        //     }
    
        //     return $queryBuilder;
        // }
    }

