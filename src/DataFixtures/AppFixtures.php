<?php

namespace App\DataFixtures;

use App\Entity\Configuration;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Profil;
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
        $faker = Factory::create('fr_FR');
        $utilisateurs = [];

        // ==========================================
        // 1. CRÉATION DE 5 MODÉRATEURS DE TEST
        // ==========================================
        for ($i = 1; $i <= 5; $i++) {
            $adminUser = new Utilisateur();
            $adminUser->setPseudo('admin' . $i)
                      ->setEmail('admin' . $i . '@tindr.fr')
                      ->setMdp($this->hasher->hashPassword($adminUser, 'admin' . $i))
                      ->setImageIdentite('PlaceHolderProfil.jpg')
                      ->setStatus(Utilisateur::STATUS_APPROVED)
                      ->setIsModo(true);
            $manager->persist($adminUser);
        }

        // ==========================================
        // 2. CRÉATION DES 5 UTILISATEURS DE TEST 
        // ==========================================
        $simpleUsers = [];
        $motifs = ['Faux profil', 'Harcèlement', 'Propos injurieux', 'Spam / Brouteur', 'Photos inappropriées'];

        for ($i = 1; $i <= 5; $i++) {
            $user = new Utilisateur();
            $user->setPseudo('user' . $i)
                 ->setEmail('user' . $i . '@tindr.fr')
                 ->setMdp($this->hasher->hashPassword($user, 'user' . $i))
                 ->setImageIdentite('Homme' . $i . '.jpeg')
                 ->setStatus(Utilisateur::STATUS_APPROVED)
                 ->setIsModo(false);
            $manager->persist($user);
            $utilisateurs[] = $user;
            $simpleUsers[] = $user; // On les isole dans un tableau spécifique

            $profil = new Profil();
            $profil->setNom('NomUser' . $i)
                   ->setPrenom('PrenomUser' . $i)
                   ->setAge(20 + $i)
                   ->setGenre('Homme')
                   ->setVille('Ville' . $i)
                   ->setPresentation('Je suis le testeur normal numéro ' . $i)
                   ->setUtilisateur($user);
            $manager->persist($profil);

            $config = new Configuration();
            $config->setAgeMin(18)->setAgeMax(99)->setRayon(50)
                   ->setGenresVisibles(['Homme', 'Femme', 'Non-binaire'])
                   ->setEtatNotif(true)->setUtilisateur($user);
            $manager->persist($config);
        }

        // ==========================================
        // 3. INTERACTIONS ET SIGNALEMENTS (EXCLUSIVEMENT ENTRE LES 5 TESTS)
        // ==========================================
        for ($i = 0; $i < 5; $i++) {
            $auteur = $simpleUsers[$i];
            $cible = $simpleUsers[($i + 1) % 5]; // user1 avec user2, user2 avec user3, etc.

            // 1. Ils se signalent
            $signalement = new Signalement();
            $signalement->setAuteur($auteur)
                        ->setCible($cible)
                        ->setMotif($motifs[$i])
                        ->setDateS(new \DateTime())
                        ->setStatut(0);
            $manager->persist($signalement);

            // 2. Ils discutent
            $conversation = new Conversation();
            $conversation->addParticipant($auteur);
            $conversation->addParticipant($cible);
            $manager->persist($conversation);

            $message1 = new Message();
            $message1->setAuthor($auteur)
                     ->setConversation($conversation)
                     ->setContent("Salut, tu m'énerves " . $cible->getPseudo() . " !")
                     ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($message1);

            $message2 = new Message();
            $message2->setAuthor($cible)
                     ->setConversation($conversation)
                     ->setContent("C'est pour ça que je t'ai signalé " . $auteur->getPseudo() . ".")
                     ->setCreatedAt((new \DateTimeImmutable())->modify('+1 minute'));
            $manager->persist($message2);
        }

        $manager->flush();

        // ==========================================
        // 4. CRÉATION D'UTILISATEURS FAKER (SANS INTERACTIONS)
        // ==========================================
        // Ils servent uniquement à remplir la base (recherche, swipe, etc.)
        $genres = ['Homme', 'Femme', 'Non-binaire'];
        
        for ($i = 0; $i < 400; $i++) { 
            $genreChoisi = $faker->randomElement($genres);
            
            $prenom = '';
            $nom = $faker->lastName();

            if ($genreChoisi === 'Homme') {
                $photoPrincipale = 'Homme' . $faker->numberBetween(1, 9) . '.jpeg';
                $prenom = $faker->firstNameMale();
            } elseif ($genreChoisi === 'Femme') {
                $photoPrincipale = 'Femme' . $faker->numberBetween(1, 9) . '.jpeg';
                $prenom = $faker->firstNameFemale();
            } else {
                $prefixe = $faker->randomElement(['Binaire']);
                $photoPrincipale = $prefixe . $faker->numberBetween(1, 9) . '.jpeg';
                $prenom = $faker->firstName();
            }

            $prenomPropre = strtolower(str_replace([' ', '-'], '', $prenom));
            $pseudo = $prenomPropre . $faker->numberBetween(10, 999);

            $user = new Utilisateur();
            $user->setPseudo($pseudo)
                 ->setEmail($faker->unique()->safeEmail())
                 ->setMdp($this->hasher->hashPassword($user, 'password'))
                 ->setImageIdentite($photoPrincipale)
                 ->setStatus(Utilisateur::STATUS_APPROVED)
                 ->setIsModo(false);
            $manager->persist($user);
            $utilisateurs[] = $user;

            $profil = new Profil();
            $profil->setNom($nom)
                   ->setPrenom($prenom)
                   ->setAge($faker->numberBetween(18, 60))
                   ->setGenre($genreChoisi)
                   ->setVille($faker->city())
                   ->setPresentation($faker->realText(150))
                   ->setUtilisateur($user);
            $manager->persist($profil);

            $config = new Configuration();
            $config->setAgeMin(18)
                   ->setAgeMax(99)
                   ->setRayon(50)
                   ->setGenresVisibles(['Homme', 'Femme', 'Non-binaire']) 
                   ->setEtatNotif(true)
                   ->setUtilisateur($user);
            $manager->persist($config);
        }
        $manager->flush();

        // ==========================================
        // 5. CRÉATION DES COMPTES AUX STATUTS SPÉCIFIQUES
        // ==========================================
        // On utilise les constantes définies dans l'entité Utilisateur
        $statutsSpecifiques = [
            'pending' => Utilisateur::STATUS_PENDING,
            'rejected' => Utilisateur::STATUS_REJECTED,
            'banned' => Utilisateur::STATUS_BANNED,
        ];

        foreach ($statutsSpecifiques as $prefix => $status) {
            for ($i = 1; $i <= 5; $i++) {
                $userSpecifique = new Utilisateur();
                $pseudo = $prefix . $i;
                
                $userSpecifique->setPseudo($pseudo)
                     ->setEmail($pseudo . '@tindr.fr')
                     ->setMdp($this->hasher->hashPassword($userSpecifique, $pseudo)) // Mot de passe = pseudo
                     ->setImageIdentite('PlaceHolderProfil.jpg')
                     ->setStatus($status)
                     ->setIsModo(false);
                $manager->persist($userSpecifique);
                $utilisateurs[] = $userSpecifique;

                // On leur crée un profil et une configuration pour éviter les erreurs d'affichage
                $profil = new Profil();
                $profil->setNom(ucfirst($prefix))
                       ->setPrenom('Testeur ' . $i)
                       ->setAge(25)
                       ->setGenre('Homme')
                       ->setVille('Paris')
                       ->setPresentation('Je suis un compte de test pour le statut : ' . strtoupper($prefix))
                       ->setUtilisateur($userSpecifique);
                $manager->persist($profil);

                $config = new Configuration();
                $config->setAgeMin(18)->setAgeMax(99)->setRayon(50)
                       ->setGenresVisibles(['Homme', 'Femme', 'Non-binaire'])
                       ->setEtatNotif(true)->setUtilisateur($userSpecifique);
                $manager->persist($config);
            }
        }
        $manager->flush();

        // ==========================================
        // 6. CRÉATION DE NOTIFICATIONS (FAKER)
        // ==========================================
        for ($i = 0; $i < 20; $i++) {
            $notif = new Notification();
            $notif->setUtilisateur($faker->randomElement($utilisateurs))
                  ->setContenu("Vous avez un nouveau Match !")
                  ->setType(1)
                  ->setLu($faker->boolean());
            $manager->persist($notif);
        }

        $manager->flush();
    }
}