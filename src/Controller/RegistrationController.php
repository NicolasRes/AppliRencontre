<?php

namespace App\Controller;

use App\Entity\Configuration;
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
use App\Entity\Notification;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {

        // Pas de new Utilisateur tout de suite, on doit distinguer le cas où l'utilisateur existe et veut modifier ses données
        $user = $this->getUser();

        // Empêche un utilisateur connecté non rejeté de recréer un compte
        // Donc on autorise un utilisateur non connecté et un utilisateur rejeté à accéder à /register
        if ($user instanceof Utilisateur && !$user->isRejected()) {
            return $this->redirectToRoute('app_home_page');
        }

        // S'il n'existe pas, on le crée
        $isNewUser = !($user instanceof Utilisateur);

        if ($isNewUser) {
            $user = new Utilisateur();
        }

        // Création du formulaire et remplissage de l'objet user avec les infos entrées
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. SETUP DE BASE DE L'UTILISATEUR

            // Changement de mot de passe uniquement s'il est renseigné
            // (pour éviter de modifier le mdp si on laisse le champ blanc lors d'une modif de formulaire par exemple
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setMdp($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            // Le statut repasse en attente après soumission s'il était rejeté auparavant ou si l'utilisateur est nouveau'
            if ($isNewUser || $user->isRejected()) {
                $user->setStatus(Utilisateur::STATUS_PENDING);
            }

            // Sécurité pour qu'un modo ne perde pas son rôle en modifiant son profil
            if ($isNewUser) {
                $user->setIsModo(false);
            }

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

            // S'il s'agit d'une modif de formulaire, on récupère le profil existant
            $profil = $entityManager->getRepository(Profil::class)
                ->findOneBy(['utilisateur' => $user]);  // Récupère le profil via l'utilisateur (comme un getProfil)

            // Sinon, on le crée
            if (!$profil) {
                $profil = new Profil();
                $profil->setUtilisateur($user);
            }

            // Pas dans le if pour que les données soient enregistrées s'il s'agit d'un utilisateur rejeté qui fait une modif
            $profil->setPrenom($form->get('prenom')->getData());
            $profil->setNom($form->get('nom')->getData());
            $profil->setAge($form->get('age')->getData());
            $profil->setGenre($form->get('genre')->getData());
            $profil->setVille("Non renseignée");
            $profil->setPresentation("Salut !");

            $entityManager->persist($profil);

            // On flush une deuxième fois pour enregistrer le nom de l'image et le profil
            //$entityManager->flush();

            // 4. CRÉATION DE LA CONFIGURATION

            // S'il s'agit d'une modif de formulaire, on récupère la configuration existante
            $config = $entityManager->getRepository(Configuration::class)
                ->findOneBy(['utilisateur' => $user]);

            // Sinon, on la crée
            if (!$config) {
                $config = new Configuration();
                $config->setUtilisateur($user);
                $config->setEtatNotif(false);
            }

            $entityManager->persist($config);

            // 5. ENVOI D'UNE NOTIFICATION DE BIENVENUE (uniquement pour nouvel utilisateur)
            if ($isNewUser) {
                $notification = new Notification();
                $notification->setUtilisateur($user); // On lie la notif à l'utilisateur fraîchement créé
                $notification->setContenu("Bienvenue ! Votre compte a été créé avec succès.");
                $notification->setType(0); // On lui donne 0 car c'est le type pour la notification de bienvenue
                $notification->setLu(false); // La notification est non lue par défaut

                $entityManager->persist($notification);
            }

            // Le flush() final va tout enregistrer en une seule fois (User, Profil, Config, Notification)
            $entityManager->flush();

            // Message de validation (géré dans le twig selon inscription ou modification)
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
