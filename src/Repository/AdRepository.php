<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @return Ad[] Returns an array of Ad objects
     */

    public function findBySearch($year, $kilometers, $price, $fuel, $model, $brand)
    {
        $query = $this->createQueryBuilder('a')
            ->Where('YEAR(a.year) BETWEEN :year1 AND  :year2')
            ->andWhere('a.kilometers BETWEEN :km1 AND :km2')
            ->andWhere('a.price BETWEEN :price1 AND  :price2')
            ->setParameters(array('year1' => $year[0],
                'year2'=>$year[1],
                'km1' => $kilometers[0],
                'km2'=>$kilometers[1],
                'price1' => $price[0],
                'price2'=>$price[1]));

        if($model !== null){
            $query -> andWhere('a.model = :model')
                ->setParameter('model', $model);
        }
        if($fuel !== null){
            $query -> andWhere('a.fuel = :fuel')
                ->setParameter('fuel', $fuel);
        }
        if($brand !== null){
            $query->leftJoin('a.model', 'model')
                ->andWhere('model.brand = :brand')
                ->setParameter('brand', $brand);
        }
        return $query->getQuery()
            ->getResult()
      ;
    }


    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
