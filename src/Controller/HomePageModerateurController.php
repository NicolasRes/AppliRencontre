<?php

namespace App\Controller;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use App\Repository\SignalementRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ProfilRepository;
use App\Repository\ConversationRepository;
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
    public function index(SignalementRepository $signalementRepository, ConversationRepository $convRepo): Response
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

        // On prépare la conversation à afficher pour le premier signalement
        $conversation = null;
        if (!empty($signalements)) {
            $premierSig = $signalements[0];
            // On récupère la conv entre l'auteur du signalement et la personne visée
            $conversation = $convRepo->findByUsers($premierSig->getAuteur(), $premierSig->getCible());
        }

        return $this->render('home_page_moderateur/index.html.twig', [
            'signalements' => $signalements,
            'conversation' => $conversation // On passe la conv à Twig pour l'historique
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

        $this->addFlash('error', "Le profil de {$utilisateur->getPseudo()} a été refusé.");
        return $this->redirectToRoute('app_moderateur_validations');
    }

    #[Route('/moderateur/bannir/{id}', name: 'app_moderateur_bannir', methods: ['GET', 'POST'])]
    public function bannir(Signalement $signalement, EntityManagerInterface $em): Response
    {
        // 1. On récupère l'utilisateur ciblé par le signalement
        $cible = $signalement->getCible();
        
        // 2. On change son statut en "banni" (assurez-vous que la constante existe dans Utilisateur)
        $cible->setStatus(Utilisateur::STATUS_BANNED);
        
        // 3. On marque le signalement comme traité (statut 1 par exemple)
        $signalement->setStatut(1);
        
        $em->flush();

        $this->addFlash('success', "L'utilisateur {$cible->getPseudo()} a été banni.");
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    #[Route('/moderateur/ignorer/{id}', name: 'app_moderateur_ignorer', methods: ['GET', 'POST'])]
    public function ignorer(Signalement $signalement, EntityManagerInterface $em): Response
    {
        // On marque simplement le signalement comme traité sans sanctionner l'utilisateur
        $signalement->setStatut(1);
        $em->flush();

        $this->addFlash('info', "Le signalement a été classé sans suite.");
        return $this->redirectToRoute('app_moderateur_signalements');
    }

    // ======================================================
    // SECTION 2 : GESTION DES PROFILS (VALIDATIONS)
    // ======================================================

    #[Route('/moderateur/validations', name: 'app_moderateur_validations')]
    public function validations(UtilisateurRepository $utilisateurRepository, ProfilRepository $profilRepo): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        if (!$user || !$user->isModo()) {
            $this->addFlash('error', 'Accès réservé aux modérateurs.');
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
}