<?php

namespace App\Tests;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfilTest extends KernelTestCase
{
    /**
     * Test de création d'un profil avec les informations de base.
     */
    public function testCreationProfilMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur obligatoire pour le lien
        $user = new Utilisateur();
        $user->setPseudo('UserProfil')
             ->setEmail('profil.test@test.com')
             ->setMdp('password123')
             ->setIsModo(false);

        // 2. Création du Profil
        $profil = new Profil();
        $profil->setAge(25)
               ->setGenre('Homme')
               ->setVille('Paris')
               ->setNom('Dupont')
               ->setPrenom('Jean')
               ->setUtilisateur($user);

        // Grâce au cascade: ['persist'], on a juste besoin de persister le profil
        $entityManager->persist($profil);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($profil->getId());
        $this->assertEquals(25, $profil->getAge());
        $this->assertEquals('Paris', $profil->getVille());
        $this->assertEquals('Dupont', $profil->getNom());
        
        // Vérification de la relation OneToOne
        $this->assertNotNull($profil->getUtilisateur());
        $this->assertEquals('UserProfil', $profil->getUtilisateur()->getPseudo());
    }

    /**
     * Test avec le champ optionnel 'presentation'.
     */
    public function testCreationProfilComplet(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = new Utilisateur();
        $user->setPseudo('JaneDoe')
             ->setEmail('jane.profil@test.com')
             ->setMdp('securepass')
             ->setIsModo(false);

        $profil = new Profil();
        $profil->setAge(30)
               ->setGenre('Femme')
               ->setVille('Lyon')
               ->setNom('Doe')
               ->setPrenom('Jane')
               ->setPresentation('Bonjour, je suis ici pour faire de nouvelles rencontres.')
               ->setUtilisateur($user);

        $entityManager->persist($profil);
        $entityManager->flush();

        // Assertions
        $this->assertEquals('Lyon', $profil->getVille());
        $this->assertEquals('Bonjour, je suis ici pour faire de nouvelles rencontres.', $profil->getPresentation());
        $this->assertInstanceOf(Utilisateur::class, $profil->getUtilisateur());
    }
}