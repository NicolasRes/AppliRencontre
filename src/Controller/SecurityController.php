<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Utilisateur;

/**
 * Contrôleur gérant l’authentification des utilisateurs et leur redirection selon leur statut
 */
class SecurityController extends AbstractController
{
    /**
     * Méthode qui gère l’authentification et redirige l’utilisateur selon son statut
     * @param AuthenticationUtils $authenticationUtils Utilitaire pour récupérer les erreurs et le dernier identifiant saisi
     * @return Response Réponse HTTP affichant le formulaire ou redirigeant selon le statut de l’utilisateur
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        // Si l'utilisateur est connecté
        if ($user instanceof Utilisateur) {

            // Redirection spécifique pour les modérateurs
            if ($user->isModo()) {
                return $this->redirectToRoute('app_moderateur_signalements');
            }

            // Redirection vers la homepage si validé
            if ($user->isApproved()) {
                return $this->redirectToRoute('app_home_page');
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Méthode qui déclenche la déconnexion de l’utilisateur via Symfony
     * @return void Ne retourne rien, la déconnexion est interceptée par Symfony
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/banned', name: 'app_banned')]
    public function banned(): Response
    {
        // On vérifie tout de même que la personne est bien connectée et bannie
        $user = $this->getUser();
        if (!$user || !$user->isBanned()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('security/banned.html.twig');
    }

}
