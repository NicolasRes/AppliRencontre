<?php

namespace App\Controller;

use App\Repository\SignalementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageModerateurController extends AbstractController
{
    #[Route('/home/page/moderateur', name: 'app_home_page_moderateur')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        // On récupère tous les signalements (on pourra filtrer par statut plus tard)
        $signalements = $signalementRepository->findAll();

        // On envoie la liste à la vue Twig
        return $this->render('home_page_moderateur/index.html.twig', [
            'signalements' => $signalements,
        ]);
    }
}