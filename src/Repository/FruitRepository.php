<?php

namespace App\Repository;

use App\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 *
 * @method Fruit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fruit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fruit[]    findAll()
 * @method Fruit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $filters
     * @return Paginator
     */
    public function getPaginatedFruits(int $page, int $limit, array $filters = []): Paginator
    {
        $query = $this->createQueryBuilder('f');
        if(!empty($filters) && isset($filters['name'])) {
            $query->andWhere('f.name Like :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }
        if(!empty($filters) && isset($filters['family'])) {
            $query->andWhere('f.family Like :family')
                ->setParameter('family', '%' . $filters['family'] . '%');
        }
        $query
            ->orderBy('f.name', 'ASC')
            ->getQuery();

        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;

    }

    public function getFavouriteFruits($favourites = [])
    {
        $results = $this->createQueryBuilder('f')
            ->where('f.id in (:favourites)')
            ->setParameter('favourites', $favourites)
            ->orderBy('f.name', 'ASC')
            ->getQuery()->getResult();

        $fruits = [];
        foreach ($results as $fruit) {
            $fruits[] = $fruit;
        }

        return $fruits;
    }
}
