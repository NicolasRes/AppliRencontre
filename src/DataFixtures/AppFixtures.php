<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use App\Entity\Liens;
use App\Entity\Message;
use App\Entity\Moderateur;
use App\Entity\Notification;
use App\Entity\PhotoProfil;
use App\Entity\Profil;
use App\Entity\Rencontre;
use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // On initialise Faker pour avoir des données en Français
        $faker = Factory::create('fr_FR');
        $utilisateurs = [];

        // ==========================================
        // 1. CRÉATION D'UN MODÉRATEUR DE TEST
        // ==========================================
        $adminUser = new Utilisateur();
        $adminUser->setPseudo('AdminModo')
                  ->setEmail('admin@tindr.fr')
                  ->setMdp($this->hasher->hashPassword($adminUser, 'admin'))
                  ->setAccordGdpr(true)
                  ->setIsModo(true);
        $manager->persist($adminUser);

        $moderateur = new Moderateur();
        $moderateur->setUtilisateur($adminUser);
        $manager->persist($moderateur);

        // ==========================================
        // 2. CRÉATION DE 30 UTILISATEURS & PROFILS
        // ==========================================
        $genres = ['Homme', 'Femme', 'Non-binaire'];
        
        for ($i = 0; $i < 30; $i++) {
            $user = new Utilisateur();
            $user->setPseudo($faker->userName())
                 ->setEmail($faker->unique()->safeEmail())
                 ->setMdp($this->hasher->hashPassword($user, 'password')) // Le mdp est "password" pour tous
                 ->setImageIdentite('PlaceHolderProfil.jpg')
                 ->setAccordGdpr(true)
                 ->setIsModo(false);
            $manager->persist($user);
            $utilisateurs[] = $user;

            // Le Profil associé
            $profil = new Profil();
            $profil->setNom($faker->lastName())       
                   ->setPrenom($faker->firstName())  
                   ->setAge($faker->numberBetween(18, 60))
                   ->setGenre($faker->randomElement($genres))
                   ->setVille($faker->city())
                   ->setPresentation($faker->realText(150))
                   ->setUtilisateur($user);
            $manager->persist($profil);

            // Ajout de 2 photos aléatoires pour le profil
            for ($j = 0; $j < 2; $j++) {
                $photo = new PhotoProfil();
                $photo->setLienPhoto('PlaceHolderProfil.jpg')
                      ->setProfil($profil);
                $manager->persist($photo);
            }

            // Génération d'un lien Premium fictif (1 chance sur 3)
            if ($faker->boolean(30)) {
                $lien = new Liens();
                $lien->setExpDate($faker->dateTimeBetween('now', '+1 year'))
                     ->setUtilise($faker->boolean())
                     ->setUtilisateur($user);
                $manager->persist($lien);
            }
            
            // Configuration de base pour l'utilisateur
            $config = new Configuration();
            $config->setAgeMin(18)
                   ->setAgeMax(99)
                   ->setRayon(50)
                   ->setGenresVisibles(['Homme', 'Femme'])
                   ->setEtatNotif(true)
                   ->setUtilisateur($user);
            $manager->persist($config);
        }

        // ==========================================
        // 3. CRÉATION DE MATCHS (RENCONTRES) & MESSAGES
        // ==========================================
        $rencontres = [];
        for ($i = 0; $i < 15; $i++) {
            // On prend 2 utilisateurs au hasard
            $u1 = $faker->randomElement($utilisateurs);
            $u2 = $faker->randomElement($utilisateurs);

            if ($u1 !== $u2) {
                $rencontre = new Rencontre();
                $rencontre->setUtilisateur($u1)
                          ->setUtilisateur2($u2)
                          ->setStatut($faker->numberBetween(0, 1)) // 0: en attente, 1: validé
                          ->setDateCreation($faker->dateTimeThisYear());
                $manager->persist($rencontre);
                $rencontres[] = $rencontre;

                // Création de messages si le match est validé
                if ($rencontre->getStatut() === 1) {
                    for ($m = 0; $m < $faker->numberBetween(2, 6); $m++) {
                        $message = new Message();
                        $auteurMsg = $faker->boolean() ? $u1 : $u2;
                        $message->setAuteur($auteurMsg)
                                ->setRencontre($rencontre)
                                ->setContenu($faker->sentence())
                                ->setTemps($faker->dateTimeBetween($rencontre->getDateCreation(), 'now'))
                                ->setEstLu($faker->boolean());
                        $manager->persist($message);
                    }
                }
            }
        }

        // ==========================================
        // 4. CRÉATION DE SIGNALEMENTS POUR LE MODO
        // ==========================================
        $motifs = ['Faux profil', 'Harcèlement', 'Propos injurieux', 'Spam / Brouteur', 'Photos inappropriées'];
        
        for ($i = 0; $i < 10; $i++) {
            $auteur = $faker->randomElement($utilisateurs);
            $cible = $faker->randomElement($utilisateurs);

            if ($auteur !== $cible) {
                $signalement = new Signalement();
                $signalement->setAuteur($auteur)
                            ->setCible($cible)
                            ->setMotif($faker->randomElement($motifs))
                            ->setDateS($faker->dateTimeThisMonth())
                            ->setStatut(0); // 0 = En attente
                $manager->persist($signalement);
            }
        }

        // ==========================================
        // 5. CRÉATION DE NOTIFICATIONS
        // ==========================================
        for ($i = 0; $i < 20; $i++) {
            $notif = new Notification();
            $notif->setUtilisateur($faker->randomElement($utilisateurs))
                  ->setContenu("Vous avez un nouveau Match !")
                  ->setType(1)
                  ->setLu($faker->boolean());
            $manager->persist($notif);
        }

        // ON SAUVEGARDE TOUT EN BASE DE DONNÉES
        $manager->flush();
    }
}