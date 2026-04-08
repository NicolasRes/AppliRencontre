<?php

namespace App\Tests;

use App\Entity\Configuration;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigurationTest extends KernelTestCase
{
    /**
     * Test Minimal : Vérifie que l'entité peut être créée avec ses champs obligatoires.
     */
    public function testCreationConfigurationMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création d'un utilisateur valide avec ses champs obligatoires
        $utilisateur = new Utilisateur();
        $utilisateur->setPseudo('UserTestMin')
                    ->setEmail('min@test.com')
                    ->setMdp('motdepasse123')
                    ->setIsModo(false);

        // 2. Création de la configuration
        $config = new Configuration();
        $config->setAgeMin(18)
               ->setAgeMax(99)
               ->setRayon(50)
               ->setEtatNotif(true)
               ->setUtilisateur($utilisateur); // On relie l'utilisateur

        // L'attribut cascade: ['persist'] sur Configuration enregistrera l'Utilisateur automatiquement
        $entityManager->persist($config);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($config->getId());
        $this->assertIsInt($config->getAgeMin());
        $this->assertEmpty($config->getGenresVisibles());
        $this->assertNotNull($config->getUtilisateur());
        $this->assertEquals('UserTestMin', $config->getUtilisateur()->getPseudo());
    }

    /**
     * Test Maximal : Vérifie l'intégrité de tous les champs, y compris le tableau JSON.
     */
    public function testCreationConfigurationMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création d'un autre utilisateur valide (email différent)
        $utilisateur = new Utilisateur();
        $utilisateur->setPseudo('UserTestMax')
                    ->setEmail('max@test.com')
                    ->setMdp('motdepasse456')
                    ->setIsModo(true);

        $genres = ['Homme', 'Femme', 'Non-binaire'];
        
        // 2. Création de la configuration
        $config = new Configuration();
        $config->setAgeMin(20)
               ->setAgeMax(35)
               ->setRayon(100)
               ->setGenresVisibles($genres)
               ->setEtatNotif(false)
               ->setUtilisateur($utilisateur); // On relie l'utilisateur

        $entityManager->persist($config);
        $entityManager->flush();

        // 3. Assertions de types et de valeurs
        $this->assertEquals(20, $config->getAgeMin());
        $this->assertEquals(35, $config->getAgeMax());
        $this->assertEquals(100, $config->getRayon());
        $this->assertFalse($config->isEtatNotif());
        
        // Vérification du tableau (stocké en JSON en base)
        $this->assertCount(3, $config->getGenresVisibles());
        $this->assertContains('Femme', $config->getGenresVisibles());
        $this->assertIsArray($config->getGenresVisibles());
        
        // Vérification de la relation forte
        $this->assertEquals($utilisateur, $config->getUtilisateur());
        $this->assertEquals('max@test.com', $config->getUtilisateur()->getEmail());
    }
}