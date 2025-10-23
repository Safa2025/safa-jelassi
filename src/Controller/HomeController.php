<?php
namespace App\Controller;

use App\Service\HappyQuote;
use App\Service\BookManagerService;
use App\Service\AuthorMailerService;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    private HappyQuote $happyQuote;
    private BookManagerService $bookManager;
    private AuthorMailerService $authorMailer;
    private AuthorRepository $authorRepository;

    public function __construct(
        HappyQuote $happyQuote,
        BookManagerService $bookManager,
        AuthorMailerService $authorMailer,
        AuthorRepository $authorRepository
    ) {
        $this->happyQuote = $happyQuote;
        $this->bookManager = $bookManager;
        $this->authorMailer = $authorMailer;
        $this->authorRepository = $authorRepository;
    }

    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        // Récupération des auteurs
        $authors = $this->authorRepository->findAll();

        // Citation du jour
        $happyMessage = $this->happyQuote->getHappyMessage();

        // Meilleurs auteurs
        $bestAuthors = $this->bookManager->bestAuthors();

        // Exemple d'envoi d'un mail pour le premier auteur
        if (!empty($authors)) {
            $firstAuthor = $authors[0];
            $this->authorMailer->notifyAuthor(
                $firstAuthor->getEmail(),
                "Exemple de livre"
            );
        }

        // Affichage dans Twig
        return $this->render('home/index.html.twig', [
            'authors' => $authors,
            'happyMessage' => $happyMessage,
            'bestAuthors' => $bestAuthors
        ]);
    }
}
