<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{

    /* Cool pour debug
    #[Route('/profiles-test', name: 'app_profiles_test')]
    public function list(ProfilRepository $profilRepository): Response
    {
        $profiles = $profilRepository->findAll();

        $lines = [];

        foreach ($profiles as $profile) {
            $lines[] = sprintf(
                'id=%d | %s %s | %d ans | %s | %s',
                $profile->getId(),
                $profile->getPrenom() ?? '',
                $profile->getNom() ?? '',
                $profile->getAge() ?? 0,
                $profile->getGenre() ?? '',
                $profile->getVille() ?? ''
            );
        }

        return new Response(
            '<pre>' . htmlspecialchars(implode("\n", $lines)) . '</pre>'
        );
    }*/

    #[Route('/profile/{id}', name: 'app_profile_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProfilRepository $profilRepository): Response
    {
        $profile = $profilRepository->find($id);

        if ($profile === null) {
            throw $this->createNotFoundException('Profil introuvable.');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }

    /**
     * Méthode qui affiche la page de recherche de profils et applique les filtres saisis par l'utilisateur
     * @param Request $request La requête HTTP
     * @param ProfilRepository $profilRepository Le repository des profils
     * @return Response Page HTML avec les résultats de la recherche
     */
    #[Route('/search', name: 'app_profile_search')]
    public function search(Request $request, ProfilRepository $profilRepository): Response
    {
        $pseudo = $request->query->get('pseudo');
        $genre = $request->query->get('genre');
        $ageMin = $request->query->get('age_min');
        $ageMax = $request->query->get('age_max');

        // On empêche l'utilisateur de faire une recherche pour un âge inférieur à 18 ans
        if($ageMin !== null && $ageMin < 18) {
            $ageMin = 18;
        }

        // Si l'âge min > l'âge max, on inverse les valeurs
        $min = (int)$ageMin;
        $max = (int)$ageMax;

        if ($min && $max && $min > $max) {
            [$ageMin, $ageMax] = [$ageMax, $ageMin];
        }

        // Construction de la liste des profils
        $profiles = $profilRepository->searchProfiles(
            $pseudo,
            $genre,
            $ageMin ? (int)$ageMin : null,
            $ageMax ? (int)$ageMax : null
        );

        return $this->render('profile/search.html.twig', [
            'profiles' => $profiles
        ]);
    }
}
