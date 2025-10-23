<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    // Compter le nombre de livres d'une catégorie donnée
    public function countBooksByCategory(string $category): int
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT COUNT(b) FROM App\Entity\Book b WHERE b.category = :cat')
            ->setParameter('cat', $category);

        return (int) $query->getSingleScalarResult();
    }

    // Récupérer les livres publiés entre deux dates
    public function findBooksBetweenDates(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT b FROM App\Entity\Book b 
                           WHERE b.published = true 
                           AND b.publicationDate BETWEEN :start AND :end')
            ->setParameters([
                'start' => $start,
                'end' => $end
            ])
            ->getResult();
    }
}
