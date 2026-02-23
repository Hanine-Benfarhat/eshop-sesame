<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters(?string $search, ?string $category, ?string $priceRange, ?string $sort = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 'approved');

        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%'.$search.'%');
        }

        if ($category) {
            $qb->andWhere('p.category = :category')
               ->setParameter('category', $category);
        }

        if ($priceRange) {
            switch ($priceRange) {
                case '0-50':
                    $qb->andWhere('p.price BETWEEN 0 AND 50');
                    break;
                case '50-200':
                    $qb->andWhere('p.price BETWEEN 50 AND 200');
                    break;
                case '200-500':
                    $qb->andWhere('p.price BETWEEN 200 AND 500');
                    break;
                case '500+':
                    $qb->andWhere('p.price >= 500');
                    break;
            }
        }

        // apply sort
        if ($sort) {
            switch ($sort) {
                case 'price_asc':
                    $qb->orderBy('p.price', 'ASC');
                    break;
                case 'price_desc':
                    $qb->orderBy('p.price', 'DESC');
                    break;
                case 'oldest':
                    $qb->orderBy('p.createdAt', 'ASC');
                    break;
                default: // newest or unrecognized
                    $qb->orderBy('p.createdAt', 'DESC');
            }
        } else {
            $qb->orderBy('p.createdAt', 'DESC');
        }

        return $qb->getQuery()
                  ->getResult();
    }

    public function findApprovedProducts(int $limit = 6): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', 'approved')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
