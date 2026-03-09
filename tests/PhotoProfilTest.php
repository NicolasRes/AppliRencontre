<?php

namespace App\Tests;

use App\Entity\PhotoProfil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhotoProfilTest extends KernelTestCase
{
    /**
     * Test minimal : Vérifie que l'entité peut être créée avec son champ obligatoire.
     */
    public function testCreationPhotoProfilMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $photo = new PhotoProfil();
        $photo->setLienPhoto("avatar_defaut.jpg");

        $entityManager->persist($photo);
        $entityManager->flush();

        // Vérification de l'ID et du type
        $this->assertNotNull($photo->getId());
        $this->assertIsInt($photo->getId());
        $this->assertEquals("avatar_defaut.jpg", $photo->getLienPhoto());
    }

    /**
     * Test maximal : Vérifie l'intégrité des données et les contraintes.
     */
    public function testCreationPhotoProfilMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $nomFichierLong = "photo_vacances_ete_2024_nancy_profil_utilisateur.png";
        
        $photo = new PhotoProfil();
        $photo->setLienPhoto($nomFichierLong);

        $entityManager->persist($photo);
        $entityManager->flush();

        // Vérification que la chaîne récupérée est identique à celle envoyée
        $this->assertNotNull($photo->getId());
        $this->assertIsString($photo->getLienPhoto());
        $this->assertEquals($nomFichierLong, $photo->getLienPhoto());
    }
}