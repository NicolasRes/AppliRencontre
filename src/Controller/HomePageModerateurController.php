<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageModerateurController extends AbstractController
{
    // ======================================================
    // SECTION 1 : GESTION DES SIGNALEMENTS
    // ======================================================

    #[Route('/moderateur/signalements', name: 'app_moderateur_signalements')]
    public function index(SignalementRepository $signalementRepository): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Sécurité : On vérifie que c'est bien un modérateur
        if (!$user || !$user->isModo()) {
            $this->addFlash('error', 'Accès refusé : cette page est réservée aux modérateurs.');
            return $this->redirectToRoute('app_home_page'); 
        }

        // On ne récupère QUE les signalements en attente (statut = 0) triés par date
        $signalements = $signalementRepository->findBy(
            ['statut' => 0], 
            ['dateS' => 'ASC']
        );

        return $this->render('home_page_moderateur/index.html.twig', [
            'signalements' => $signalements,
        ]);
    }

    #[Route('/moderateur/signalement/{id}/ignorer', name: 'app_moderateur_ignorer')]
    public function ignorer(Signalement $signalement, EntityManagerInterface $em): Response
    {
        // On supprime directement la ligne du signalement de la BDD
        $em->remove($signalement);
        $em->flush();

        $this->addFlash('success', 'Le signalement a été ignoré et supprimé de la base.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    #[Route('/moderateur/signalement/{id}/bannir', name: 'app_moderateur_bannir')]
    public function bannir(Signalement $signalement, EntityManagerInterface $em): Response
    {
        // 1. On récupère la personne signalée
        $cible = $signalement->getCible();
        
        // 2. On change son statut pour le nouveau statut BANNED
        $cible->setStatus(Utilisateur::STATUS_BANNED);
        
        // 3. On supprime la ligne du signalement puisqu'il a été traité
        $em->remove($signalement);

        $em->flush();

        $this->addFlash('danger', 'Le profil de ' . $cible->getPseudo() . ' a été banni définitivement.');
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    // ======================================================
    // SECTION 2 : VALIDATIONS DES NOUVEAUX PROFILS
    // ======================================================

    #[Route('/moderateur/validations', name: 'app_moderateur_validations')]
    public function validations(UtilisateurRepository $utilisateurRepository, ProfilRepository $profilRepo): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        if (!$user || !$user->isModo()) {
            $this->addFlash('error', 'Accès refusé : cette page est réservée aux modérateurs.');
            return $this->redirectToRoute('app_home_page'); 
        }

        $utilisateursEnAttente = $utilisateurRepository->findBy(['status' => Utilisateur::STATUS_PENDING]);
        
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
        $utilisateur->setStatus(Utilisateur::STATUS_APPROVED);
        $em->flush();

        $this->addFlash('success', "Le profil de {$utilisateur->getPseudo()} a été validé.");
        return $this->redirectToRoute('app_moderateur_validations');
    }

    #[Route('/moderateur/refuser/{id}', name: 'app_moderateur_refuser', methods: ['POST'])]
    public function refuser(Utilisateur $utilisateur, EntityManagerInterface $em): Response
    {
        $utilisateur->setStatus(Utilisateur::STATUS_REJECTED);
        $em->flush();

        $this->addFlash('warning', "Le profil de {$utilisateur->getPseudo()} a été invalidé.");
        return $this->redirectToRoute('app_moderateur_validations');
    }
}