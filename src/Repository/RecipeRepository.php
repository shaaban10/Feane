<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }
    public function findByTitleOrContent(string $query): PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.title LIKE :query OR r.content LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        return $this->paginator->paginate(
            $queryBuilder,
            1, // Assuming you always start from page 1
            10 /**limit des elements par page*/
        );
    }
    public function paginateRecipe(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('r'),
            $page,
            10, /**lilit des elements par page*/

        );

    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
