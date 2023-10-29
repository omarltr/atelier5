<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findOneById( $id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id LIKE :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findBooksPublishedBetweenDates(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->createQueryBuilder('b')
            ->where('b.publicationDate >= :startDate')
            ->andWhere('b.publicationDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }
    public function findSumScienceFictionBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('SUM(b.published) as totalPublished')
            ->where('b.category = :category')
            ->setParameter('category', 'Science Fiction')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findBooksBefore2023ByAuthorsWithMoreThan35Books()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b.title', 'b.publicationDate', 'a.username')
            ->join('b.Author', 'a')
            ->where('b.publicationDate < :year')
            ->andWhere('a.nb_books > 35')
            ->setParameter('year', '2023-01-01')
            ->getQuery();

        return $qb->getResult();
    }
    public function orderByUserName(){
        return $this->createQueryBuilder('a')->orderBy('a.title','ASC')->getQuery()->getResult();
    }
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
