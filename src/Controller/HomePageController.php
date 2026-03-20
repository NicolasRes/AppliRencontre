<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Entity\Rencontre;
use App\Entity\Utilisateur;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/home', name: 'app_home_page')]
    public function index(EntityManagerInterface $em, ProfilRepository $profilRepository): Response
    {
        // Récupération de l'utilisateur connecté
        $user = $this->getUser();
        $config = $em->getRepository(Configuration::class)->findOneBy(['utilisateur' => $user]);

        $user = $this->getUser();

        // Sécurité : on empêche les accès non autorisés
        // Utilisateur non connecté : login
        if (!$user instanceof Utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        // Rejeté : page de correction de formulaire disponible
        elseif ($user->isRejected()) {
            return $this->redirectToRoute('app_register');
        }

        // Utilisation de notre nouvelle méthode de filtrage
        $profils = $profilRepository->findProfilsNonSwipes($config, $user);

        // On mélange et on prend le premier
        $profilAffiche = !empty($profils) ? $profils[array_rand($profils)] : null;

        return $this->render('home_page/index.html.twig', [
            'profil' => $profilAffiche,
        ]);
    }

    #[Route('/swipe/{id}/{action}', name: 'app_swipe')]
    public function swipe(Utilisateur $cible, string $action, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $rencontre = new Rencontre();
        $rencontre->setUtilisateur($user);
        $rencontre->setUtilisateur2($cible);
        $rencontre->setDateCreation(new \DateTime());

        // 1 = Like, 0 = Dislike
        $rencontre->setStatut($action === 'like' ? 1 : 0);

        $em->persist($rencontre);
        $em->flush();

        return $this->redirectToRoute('app_home_page');
    }
}
