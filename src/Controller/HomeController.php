<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Ajout requis
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user instanceof Utilisateur) {

            // Empêche l'utilisateur d'aller sur /login alors qu'il est connecté
            if ($user->isApproved()) {
                return $this->render('app_home_page');  // Le twig de HomeController n'est jamais utilisé donc on redirige direct vers l'app une fois connecté
            }
            elseif ($user->isPending()) {
                return $this->render('security/waiting_validation.html.twig');
            }
            elseif ($user->isRejected()) {
                return $this->redirectToRoute('app_register');
            }
        }

        return $this->render('home/index.html.twig');
    }
}
