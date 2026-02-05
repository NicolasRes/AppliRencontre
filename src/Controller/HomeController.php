<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Ajout requis
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController 
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Si l'utilisateur est connecté mais n'a pas validé le GDPR (validation modo)
        if ($user && !$user->isAccordGdpr()) {
            // On le redirige ou on affiche le message d'attente
            return $this->render('security/waiting_validation.html.twig');
        }

        return $this->render('home/index.html.twig');
    }
}