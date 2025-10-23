<?php
// src/Service/BookManagerService.php
namespace App\Service;

use App\Repository\AuthorRepository;

class BookManagerService
{
    private AuthorRepository $authorRepository;

    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    public function countBooksByAuthor($author): int
    {
        return $author->getBooks()->count();
    }

    public function bestAuthors(): array
    {
        $authors = $this->authorRepository->findAll();
        return array_filter($authors, fn($a) => $a->getBooks()->count() > 3);
    }
}
