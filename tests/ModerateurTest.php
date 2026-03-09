<?php

namespace App\Tests;

use App\Entity\Moderateur;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModerateurTest extends KernelTestCase
{
    /**
     * Test Minimal : Vérifie la création avec le strict nécessaire (Utilisateur lié).
     */
    public function testCreationModerateurMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur (obligatoire pour le OneToOne)
        $user = (new Utilisateur())
            ->setEmail('modo.min@test.fr')
            ->setPseudo('ModoMin')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(true); // Logiquement true pour un modérateur
        
        $entityManager->persist($user);

        // 2. Création du modérateur lié
        $moderateur = new Moderateur();
        $moderateur->setUtilisateur($user);

        $entityManager->persist($moderateur);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($moderateur->getId());
        $this->assertIsInt($moderateur->getId());
        $this->assertEquals($user, $moderateur->getUtilisateur());
    }

    /**
     * Test Maximal : Vérifie l'intégrité des données et la relation.
     */
    public function testCreationModerateurMaximal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur
        $user = (new Utilisateur())
            ->setEmail('modo.max@test.fr')
            ->setPseudo('ModoMax')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(true);
        
        $entityManager->persist($user);

        // 2. Création du modérateur
        $moderateur = new Moderateur();
        $moderateur->setUtilisateur($user);

        $entityManager->persist($moderateur);
        $entityManager->flush();

        // 3. Vérifications poussées
        $this->assertNotNull($moderateur->getId());
        
        // Vérification de la liaison : l'email de l'utilisateur lié au modo est-il correct ?
        $this->assertEquals('modo.max@test.fr', $moderateur->getUtilisateur()->getEmail());
        
        // Vérification de l'instance
        $this->assertInstanceOf(Moderateur::class, $moderateur);
        $this->assertInstanceOf(Utilisateur::class, $moderateur->getUtilisateur());
    }
}