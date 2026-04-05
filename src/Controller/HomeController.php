<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        // 1. Si l'utilisateur EST connecté, on gère son cas selon son statut
        if ($user instanceof Utilisateur) {

            if ($user->isApproved()) {
                // 💡 CORRECTION ICI : redirectToRoute au lieu de render
                return $this->redirectToRoute('app_home_page');  
            }
            elseif ($user->isPending()) {
                return $this->render('security/waiting_validation.html.twig');
            }
            elseif ($user->isRejected()) {
                return $this->redirectToRoute('app_register');
            }
            elseif ($user->isBanned()) {
                return $this->render('security/banned.html.twig');
            }
        }

        // Si l'utilisateur N'EST PAS connecté (visiteur), on affiche la page d'accueil publique
        return $this->render('home/index.html.twig');
    }
}