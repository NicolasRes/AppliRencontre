<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageModerateurController extends AbstractController
{
    #[Route('/home/page/moderateur', name: 'app_home_page_moderateur')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // On vérifie directement le booléen !
        // Si l'utilisateur n'est pas connecté OU que isModo() est à false (0)
        if (!$user || !$user->isModo()) {

            $this->addFlash('error', 'Accès refusé : cette page est réservée aux modérateurs.');
            
            // On le renvoie vers la page d'accueil classique
            return $this->redirectToRoute('app_home_page'); 
            
        }

        // Si on arrive ici, c'est que isModo est bien à true (1) !
        $signalements = $signalementRepository->findAll();

        return $this->render('home_page_moderateur/index.html.twig', [
            'signalements' => $signalements,
        ]);
    }
}