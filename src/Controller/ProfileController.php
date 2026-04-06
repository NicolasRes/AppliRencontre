<?php

namespace App\Controller;

use App\Entity\PhotoProfil;
use App\Entity\Profil;
use App\Form\ModifInformationsType;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{

    /**
     * Méthode qui affiche un profil en particulier (sous forme de carte)
     * @param int $id Identifiant du profil
     * @param ProfilRepository $profilRepository Le repository des profils
     * @return Response Page HTML du profil
     */
    #[Route('/profile/{id}', name: 'app_profile_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProfilRepository $profilRepository): Response
    {
        $profile = $profilRepository->find($id);

        if ($profile === null) {
            throw $this->createNotFoundException('Profil introuvable.');
        }

        return $this->render('partials/_card.html.twig', [
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

    #[Route('informations', name: 'app_informations')]
    public function setInformations(EntityManagerInterface $em, Request $request): Response {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $infosUser = $em->getRepository(Profil::class)->findOneBy(['utilisateur' => $user]);
        $form = $this->createForm(ModifInformationsType::class, $infosUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // On récupere les photos insérés
            $listePhotos = $form->get('photoProfils')->getData();
            if($listePhotos){
                foreach ($listePhotos as $photo) {
                    // On fait un nom unique pour chaque photo dans la liste
                    $nomPhoto = uniqid(). '.' . $photo->guessExtension();
                    // On la déplace dans le dossier avec toutes les autres images, avec le nom fabriqué
                    $photo->move($this->getParameter('identite_directory'), $nomPhoto);
                    // On créer la PhotoProfil associé
                    $photoProfil = new PhotoProfil();
                    $photoProfil->setLienPhoto($nomPhoto);
                    $photoProfil->setProfil($infosUser);
                    $em->persist($photoProfil);
                }
            }
            $em->persist($infosUser);
            $em->flush();
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('profile/informations.html.twig', [
            'formulaire' => $form->createView(),
            'profil' => $infosUser
        ]);
    }

    #[Route('delete/{id}', name: 'app_photo_delete')]
    public function deletePhoto(EntityManagerInterface $em, PhotoProfil $photo): Response {
        if($photo->getProfil()->getUtilisateur() !== $this->getUser()){
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette photo.');
        }
        $cheminFichier = $this->getParameter('identite_directory') . '/' . $photo->getLienPhoto();
        if(file_exists($cheminFichier)){
            unlink($cheminFichier);
        }
        $em->remove($photo);
        $em->flush();
        return $this->redirectToRoute('app_informations');
    }
}
