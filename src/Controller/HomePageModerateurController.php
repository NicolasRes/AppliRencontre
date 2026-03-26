<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HomePageModerateurController extends AbstractController
{
    #[Route('/moderateur/signalements', name: 'app_moderateur_signalements')]
    public function index(SignalementRepository $signalementRepository, UtilisateurRepository $utilisateurRepository): Response
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

    #[Route('/moderateur/validations', name: 'app_moderateur_validations')]
    public function validations(UtilisateurRepository $utilisateurRepo, ProfilRepository $profilRepo): Response
    {
        // 1. Récupérer tous les utilisateurs en attente
        $utilisateursEnAttente = $utilisateurRepo->findBy(['status' => Utilisateur::STATUS_PENDING]);
        
        // 2. Récupérer le profil associé au premier utilisateur (si on veut l'afficher en détail)
        $profilCible = null;
        if (!empty($utilisateursEnAttente)) {
            $premierUtilisateur = $utilisateursEnAttente[0];
            $profilCible = $profilRepo->findOneBy(['utilisateur' => $premierUtilisateur]);
        }

        return $this->render('home_page_moderateur/validations.html.twig', [
            'utilisateurs' => $utilisateursEnAttente,
            'profilCible' => $profilCible
        ]);
    }

    #[Route('/moderateur/valider/{id}', name: 'app_moderateur_valider', methods: ['POST'])]
    public function valider(Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        // On passe le statut en approuvé
        $utilisateur->setStatus(Utilisateur::STATUS_APPROVED);
        $em->flush();

        $this->addFlash('success', "Le profil de {$utilisateur->getPseudo()} a été validé.");
        return $this->redirectToRoute('app_moderateur_validations');
    }

    #[Route('/moderateur/refuser/{id}', name: 'app_moderateur_refuser', methods: ['POST'])]
    public function refuser(Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        // On passe le statut en refusé (Soft Delete)
        $utilisateur->setStatus(Utilisateur::STATUS_REJECTED);
        $em->flush();

        $this->addFlash('warning', "Le profil de {$utilisateur->getPseudo()} a été invalidé.");
        return $this->redirectToRoute('app_moderateur_validations');
    }
}