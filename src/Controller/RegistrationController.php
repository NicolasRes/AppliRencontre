<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // 1. SETUP DE BASE DE L'UTILISATEUR
            $user->setMdp($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $user->setAccordGdpr(false);
            $user->setIsModo(false);

            // On persiste et on FLUSH ici pour générer l'ID dans la base
            $entityManager->persist($user);
            $entityManager->flush(); 

            // Maintenant $user->getId() est disponible !

            // 2. GESTION DE LA PHOTO AVEC L'ID
            $imageFile = $form->get('imageIdentite')->getData();
            if ($imageFile) {
                // On crée le nom : ID + extension (ex: 42.jpg)
                $newFilename = $user->getId() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('identite_directory'),
                        $newFilename
                    );
                    
                    // On met à jour le nom du fichier dans l'entité
                    $user->setImageIdentite($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'image.');
                }
            }

            // 3. CRÉATION DU PROFIL
            $profil = new Profil();
            $profil->setUtilisateur($user);
            $profil->setPrenom($form->get('prenom')->getData());
            $profil->setNom($form->get('nom')->getData());
            $profil->setAge($form->get('age')->getData());
            $profil->setGenre($form->get('genre')->getData());
            $profil->setVille("Non renseignée");
            $profil->setPresentation("Salut !");

            $entityManager->persist($profil);
            
            // On flush une deuxième fois pour enregistrer le nom de l'image et le profil
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}