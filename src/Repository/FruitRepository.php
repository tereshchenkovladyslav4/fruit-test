<?php

namespace App\Repository;

use App\Entity\Fruit;
use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;

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

    public function save(Fruit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Fruit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearchQuery(User $user, string $query, int $page = 1, string $family = null, bool $favorite = false): Paginator
    {
        $searchTerms = $this->extractSearchTerms($query);

        $qb = $this->createQueryBuilder('f')
                   ->addSelect('l')
                   ->leftJoin('f.likeUsers', 'l', Join::WITH, 'l.id = :userId')
                   ->orderBy('f.id', 'ASC')
                   ->setParameter('userId', $user->getId());

        $orX = $qb->expr()->orX();
        foreach ($searchTerms as $key => $term) {
            $orX->add($qb->expr()->like('f.name', $qb->expr()->literal('%' . $term . '%')));
        }
        if (count($searchTerms)) {
            $qb->andWhere($orX);
        }

        if ($family) {
            $qb->andWhere('f.family LIKE :family')->setParameter('family', '%' . $family . '%');
        }

        if ($favorite) {
            $qb->andWhere('l.id > 0');
        }

        return (new Paginator($qb))->paginate($page);
    }

    /**
     * Transforms the search string into an array of search terms.
     *
     * @return string[]
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique($searchQuery->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, static function ($term) {
            return 2 <= $term->length();
        });
    }
}
