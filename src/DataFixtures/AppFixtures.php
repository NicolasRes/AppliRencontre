<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Signalement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Création de l'auteur du signalement
        $user1 = new Utilisateur();
        $user1->setPseudo('Victime123');
        $user1->setEmail('v@test.com');
        $user1->setMdp('123'); // Idéalement haché, mais pour test ça passe
        $manager->persist($user1);

        // 2. Création de la personne signalée (le suspect)
        $user2 = new Utilisateur();
        $user2->setPseudo('Suspect99');
        $user2->setEmail('s@test.com');
        $user2->setMdp('123');
        $manager->persist($user2);

        // 3. Création du signalement lié aux deux utilisateurs
        $sig = new Signalement();
        $sig->setAuteur($user1);
        $sig->setCible($user2);
        $sig->setMotif('Harcèlement répété');
        $sig->setDateS(new \DateTime());
        $sig->setStatut(0); // 0 = En attente
        $manager->persist($sig);

        // On envoie tout en base de données
        $manager->flush();
    }
}