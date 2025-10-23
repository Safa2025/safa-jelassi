<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    // 🔍 Méthode DQL : afficher tous les auteurs triés par username croissant
    public function ShowAllAuthorsDQL(): array
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT a
                FROM App\Entity\Author a
                WHERE a.username LIKE :condition
                ORDER BY a.username ASC
            ')
            ->setParameter('condition', '%a%');

        return $query->getResult();
    }

    // 🔢 Rechercher les auteurs selon un intervalle du nombre de livres
    public function findAuthorsByBookRange(int $min, int $max): array
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT a 
                FROM App\Entity\Author a 
                WHERE a.nbBooks BETWEEN :min AND :max 
                ORDER BY a.nbBooks DESC
            ')
            ->setParameters([
                'min' => $min,
                'max' => $max
            ]);

        return $query->getResult();
    }
}
