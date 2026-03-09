<?php

namespace App\Tests;

use App\Entity\Configuration;
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

        $config = new Configuration();
        $config->setAgeMin(18)
               ->setAgeMax(99)
               ->setRayon(50)
               ->setEtatNotif(true);

        $entityManager->persist($config);
        $entityManager->flush();

        $this->assertNotNull($config->getId());
        $this->assertIsInt($config->getAgeMin());
        $this->assertEmpty($config->getGenresVisibles()); // Par défaut, le tableau est vide
    }

    /**
     * Test Maximal : Vérifie l'intégrité de tous les champs, y compris le tableau JSON.
     */
    public function testCreationConfigurationMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $genres = ['Homme', 'Femme', 'Non-binaire'];
        
        $config = new Configuration();
        $config->setAgeMin(20)
               ->setAgeMax(35)
               ->setRayon(100)
               ->setGenresVisibles($genres)
               ->setEtatNotif(false);

        $entityManager->persist($config);
        $entityManager->flush();

        // Assertions de types et de valeurs
        $this->assertEquals(20, $config->getAgeMin());
        $this->assertEquals(35, $config->getAgeMax());
        $this->assertEquals(100, $config->getRayon());
        $this->assertFalse($config->isEtatNotif());
        
        // Vérification du tableau (stocké en JSON en base)
        $this->assertCount(3, $config->getGenresVisibles());
        $this->assertContains('Femme', $config->getGenresVisibles());
        $this->assertIsArray($config->getGenresVisibles());
    }
}