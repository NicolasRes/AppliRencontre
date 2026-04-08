<?php

namespace App\Tests;

use App\Entity\PhotoProfil;
use App\Entity\Profil;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PhotoProfilTest extends KernelTestCase
{
    public function testCreationPhotoProfil(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'Utilisateur (requis par Profil)
        $user = new Utilisateur();
        $user->setPseudo('PhotoUser')
             ->setEmail('photo.test@test.com')
             ->setMdp('password123')
             ->setIsModo(false);
        $entityManager->persist($user);

        // 2. Création du Profil (requis par PhotoProfil)
        $profil = new Profil();
        $profil->setAge(22)
               ->setGenre('Autre')
               ->setVille('Bordeaux')
               ->setNom('Test')
               ->setPrenom('Photo')
               ->setUtilisateur($user);
        $entityManager->persist($profil);

        // 3. Création de la PhotoProfil
        $photo = new PhotoProfil();
        $photo->setLienPhoto('images/photos/ma-photo.jpg')
              ->setProfil($profil);

        $entityManager->persist($photo);
        $entityManager->flush();

        // 4. Assertions
        $this->assertNotNull($photo->getId());
        $this->assertEquals('images/photos/ma-photo.jpg', $photo->getLienPhoto());
        
        // Vérification des relations en cascade
        $this->assertSame($profil, $photo->getProfil());
        $this->assertEquals('PhotoUser', $photo->getProfil()->getUtilisateur()->getPseudo());
    }
}