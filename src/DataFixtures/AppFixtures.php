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
                  ->setImageIdentite('PlaceHolderProfil.jpg')
                  ->setStatus(Utilisateur::STATUS_APPROVED)
                  ->setIsModo(true);
        $manager->persist($adminUser);

        // ==========================================
        // 2. CRÉATION DES COMPTES DE TEST FIXES
        // ==========================================
        
        // --- COMPTE 1 : Le signaleur (Auteur) ---
        $testUser1 = new Utilisateur();
        $testUser1->setPseudo('Testeur1')
                 ->setEmail('test1@tindr.fr')
                 ->setMdp($this->hasher->hashPassword($testUser1, 'password'))
                 ->setImageIdentite('Homme1.jpeg')
                 ->setStatus(Utilisateur::STATUS_APPROVED)
                 ->setIsModo(false);
        $manager->persist($testUser1);
        $utilisateurs[] = $testUser1;

        $testProfil1 = new Profil();
        $testProfil1->setNom('Test')
                   ->setPrenom('Jean')
                   ->setAge(25)
                   ->setGenre('Homme')
                   ->setVille('Paris')
                   ->setPresentation('Je suis le compte de test gentil !')
                   ->setUtilisateur($testUser1);
        $manager->persist($testProfil1);

        $testConfig1 = new Configuration();
        $testConfig1->setAgeMin(18)->setAgeMax(99)->setRayon(50)
                   ->setGenresVisibles(['Homme', 'Femme', 'Non-binaire'])
                   ->setEtatNotif(true)->setUtilisateur($testUser1);
        $manager->persist($testConfig1);

        // --- COMPTE 2 : Le signalé (Cible) ---
        $testUser2 = new Utilisateur();
        $testUser2->setPseudo('BadGuy99')
                 ->setEmail('badguy@tindr.fr')
                 ->setMdp($this->hasher->hashPassword($testUser2, 'password'))
                 ->setImageIdentite('Homme2.jpeg')
                 ->setStatus(Utilisateur::STATUS_APPROVED)
                 ->setIsModo(false);
        $manager->persist($testUser2);
        $utilisateurs[] = $testUser2;

        $testProfil2 = new Profil();
        $testProfil2->setNom('Toxic')
                   ->setPrenom('Marc')
                   ->setAge(30)
                   ->setGenre('Homme')
                   ->setVille('Lyon')
                   ->setPresentation('Je suis le profil toxique qui va être banni !')
                   ->setUtilisateur($testUser2);
        $manager->persist($testProfil2);

        $testConfig2 = new Configuration();
        $testConfig2->setAgeMin(18)->setAgeMax(99)->setRayon(50)
                   ->setGenresVisibles(['Homme', 'Femme', 'Non-binaire'])
                   ->setEtatNotif(true)->setUtilisateur($testUser2);
        $manager->persist($testConfig2);

        $manager->flush();

        // ==========================================
        // 3. CRÉATION DE 30 UTILISATEURS & PROFILS
        // ==========================================
        $genres = ['Homme', 'Femme', 'Non-binaire'];
        
        for ($i = 0; $i < 300; $i++) {
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

            if (($i % 10) === 0) {
                $manager->flush();
            }
        }
        $manager->flush();

        // ==========================================
        // 4. CRÉATION DE SIGNALEMENTS POUR LE MODO
        // ==========================================
        $motifs = ['Faux profil', 'Harcèlement', 'Propos injurieux', 'Spam / Brouteur', 'Photos inappropriées'];
        
        $signalementFixe = new Signalement();
        $signalementFixe->setAuteur($testUser1)
                        ->setCible($testUser2)
                        ->setMotif('Propos injurieux')
                        ->setDateS(new \DateTime())
                        ->setStatut(0);
        $manager->persist($signalementFixe);

        for ($i = 0; $i < 10; $i++) {
            $auteur = $faker->randomElement($utilisateurs);
            $cible = $faker->randomElement($utilisateurs);

            if ($auteur !== $cible) {
                $signalement = new Signalement();
                $signalement->setAuteur($auteur)
                            ->setCible($cible)
                            ->setMotif($faker->randomElement($motifs))
                            ->setDateS($faker->dateTimeThisMonth())
                            ->setStatut(0);
                $manager->persist($signalement);
            }
        }
        $manager->flush();

        // ==========================================
        // 5. CRÉATION DE CONVERSATIONS & MESSAGES
        // ==========================================
        for ($i = 0; $i < 15; $i++) {
            $u1 = $faker->randomElement($utilisateurs);
            $u2 = $faker->randomElement($utilisateurs);

            if ($u1 !== $u2) {
                // 💡 On crée une Conversation et on y ajoute les participants
                $conversation = new Conversation();
                $conversation->addParticipant($u1);
                $conversation->addParticipant($u2);
                
                $manager->persist($conversation);

                // On génère des messages pour cette conversation
                for ($m = 0; $m < $faker->numberBetween(2, 6); $m++) {
                    $message = new Message();
                    $auteurMsg = $faker->boolean() ? $u1 : $u2;
                    
                    // 💡 Conversion du DateTime de Faker en DateTimeImmutable
                    $dateCreation = \DateTimeImmutable::createFromMutable($faker->dateTimeThisYear());

                    $message->setAuthor($auteurMsg)
                            ->setConversation($conversation)
                            ->setContent($faker->sentence())
                            ->setCreatedAt($dateCreation);
                            
                    $manager->persist($message);
                }
            }
            if (($i % 5) === 0) {
                $manager->flush();
            }
        }
        $manager->flush();

        // ==========================================
        // 6. CRÉATION DE NOTIFICATIONS
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