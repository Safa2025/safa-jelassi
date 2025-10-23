<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    // Liste des livres publiés et statistiques
    #[Route('/list', name: 'book_list')]
    public function list(BookRepository $repo): Response
    {
        // Récupérer les livres publiés
        $books = $repo->findBy(['published' => true]);

        // Compter les livres publiés et non publiés
        $publishedCount = $repo->count(['published' => true]);
        $unpublishedCount = $repo->count(['published' => false]);

        return $this->render('book/list.html.twig', [
            'books' => $books,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
    }

    // Ajouter un livre via formulaire
    #[Route('/new', name: 'book_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $book->setPublished(true); // par défaut publié

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Incrémenter le nbBooks de l'auteur
            $author = $book->getAuthor();
            $author->setNbBooks($author->getNbBooks() + 1);

            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès !');

            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Modifier un livre
    #[Route('/edit/{id}', name: 'book_edit')]
    public function edit(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Livre modifié avec succès !');
            return $this->redirectToRoute('book_list');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Supprimer un livre
    #[Route('/delete/{id}', name: 'book_delete')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();
        if ($author) {
            $author->setNbBooks(max(0, $author->getNbBooks() - 1));
        }

        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Livre supprimé avec succès !');

        return $this->redirectToRoute('book_list');
    }

    // Afficher les détails d’un livre
    #[Route('/{id}', name: 'book_show')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

#[Route('/count/romance', name: 'book_count_romance')]
public function countRomance(BookRepository $repo): Response
{
    $count = $repo->countBooksByCategory('Romance');

    return $this->render('book/count.html.twig', [
        'count' => $count,
        'category' => 'Romance'
    ]);
}

#[Route('/between', name: 'book_between')]
public function booksBetweenDates(BookRepository $repo): Response
{
    $books = $repo->findBooksBetweenDates(
        new \DateTime('2014-01-01'),
        new \DateTime('2018-12-31')
    );

    return $this->render('book/between.html.twig', [
        'books' => $books
    ]);
}


}
