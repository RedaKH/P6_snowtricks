<?php

namespace App\Repository;

use App\Entity\Comments;
use App\Entity\Tricks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public const NB_PER_PAGE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comments $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Comments $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function getCommentsForArticleByCreationDate(int $tricksId, int $page = 1, int $nmResults = self::NB_PER_PAGE): ?array
    {
        $offset = ($page -1) * $nmResults;
        return $this->createQueryBuilder('c')
            ->where('c.tricks = :tricksId')
            ->setParameter('tricksId', $tricksId)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->setMaxResults($nmResults)
            ->setFirstResult($offset)
            ->getResult()
            ;
    }
    
    public function getTotalNumberOfCommentsForATrick(Tricks $tricks) : int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.tricks = :tricksId')
            ->setParameter('tricksId', $tricks->getId())
            ->getQuery()->getSingleScalarResult();
    }

    public function getNbOfPages(Tricks $tricks) : int
    {
        $totalCount = $this->getTotalNumberOfCommentsForATrick($tricks);
        if ($totalCount <= self::NB_PER_PAGE) {
            return 1;
        }
        return $totalCount % self::NB_PER_PAGE === 0 ? $totalCount / self::NB_PER_PAGE : ceil($totalCount / self::NB_PER_PAGE);
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
