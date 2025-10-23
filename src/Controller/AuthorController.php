<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    // Liste des auteurs
    #[Route('/list', name: 'author_list')]
    public function list(AuthorRepository $repo): Response
    {
        $authors = $repo->findAll();

        return $this->render('author/list.html.twig', [
            'authors' => $authors
        ]);
    }

    // Ajouter un auteur avec données statiques
    #[Route('/add-static', name: 'author_add_static')]
    public function addStatic(EntityManagerInterface $em): Response
    {
        $author = new Author();
        $author->setUsername('Albert Camus');
        $author->setEmail('albert.camus@gmail.com');
        $author->setNbBooks(0);

        $em->persist($author);
        $em->flush();

        return $this->redirectToRoute('author_list');
    }

    // Ajouter un auteur via formulaire
    #[Route('/new', name: 'author_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Modifier un auteur
    #[Route('/edit/{id}', name: 'author_edit')]
    public function edit(Author $author, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('author_list');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Supprimer un auteur
    #[Route('/delete/{id}', name: 'author_delete')]
    public function delete(Author $author, EntityManagerInterface $em): Response
    {
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('author_list');
    }

    // Supprimer tous les auteurs dont nbBooks = 0
    #[Route('/cleanup', name: 'author_cleanup')]
    public function cleanup(EntityManagerInterface $em, AuthorRepository $repo): Response
    {
        $authors = $repo->findBy(['nbBooks' => 0]);

        foreach ($authors as $author) {
            $em->remove($author);
        }
        $em->flush();

        return $this->redirectToRoute('author_list');
    }

    // Rechercher les auteurs selon le nombre de livres
    #[Route('/search', name: 'author_search')]
    public function search(Request $request, AuthorRepository $repo): Response
    {
        $min = $request->query->getInt('min', 0);
        $max = $request->query->getInt('max', 100);

        $authors = $repo->findAuthorsByBookRange($min, $max);

        return $this->render('author/search.html.twig', [
            'authors' => $authors,
            'min' => $min,
            'max' => $max
        ]);
    }

    // Afficher un auteur
    #[Route('/show/{id}', name: 'show_author')]
    public function show(Author $author): Response
    {
        return $this->render('author/showAuthor.html.twig', [
            'author' => $author
        ]);
    }
}
