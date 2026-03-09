<?php

namespace App\Tests;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProfilTest extends KernelTestCase
{
    /**
     * Test avec le strict minimum : champs obligatoires uniquement.
     */
    public function testCreationProfilMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur lié (obligatoire pour le OneToOne)
        $user = (new Utilisateur())
            ->setEmail('profil.min@test.fr')
            ->setPseudo('UserMin')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);
        
        $entityManager->persist($user);

        // 2. Création du profil avec les champs NOT NULL (age, genre, ville)
        $profil = new Profil();
        $profil->setAge(25)
               ->setGenre('Non-binaire')
               ->setVille('Nancy')
               ->setUtilisateur($user);

        $entityManager->persist($profil);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($profil->getId());
        $this->assertEquals(25, $profil->getAge());
        $this->assertNull($profil->getPresentation()); // Champ optionnel doit être null
    }

    /**
     * Test avec tous les champs possibles (complet).
     */
    public function testCreationProfilMaximal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = (new Utilisateur())
            ->setEmail('profil.max@test.fr')
            ->setPseudo('UserMax')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);
        
        $entityManager->persist($user);

        $presentation = "Bonjour, je suis ici pour faire de nouvelles rencontres passionnantes !";
        
        $profil = new Profil();
        $profil->setAge(30)
               ->setGenre('Femme')
               ->setVille('Paris')
               ->setPresentation($presentation)
               ->setUtilisateur($user);

        $entityManager->persist($profil);
        $entityManager->flush();

        // Vérifications de l'intégrité des données
        $this->assertNotNull($profil->getId());
        $this->assertEquals('Femme', $profil->getGenre());
        $this->assertEquals($presentation, $profil->getPresentation());
        $this->assertIsString($profil->getPresentation());
        
        // Vérification de la liaison OneToOne
        $this->assertEquals($user->getEmail(), $profil->getUtilisateur()->getEmail());
    }
}