<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Utilisateur;


class UtilisateurTest extends KernelTestCase{

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

     public function testUploadFichier(){

        self::bootKernel();
        $container = static::getContainer();
        $uploadPath = $container->getParameter('uploads_directory');

        // On crée un faux fichier pour le test
        $filePath = $uploadPath . '/test_image.jpg';
        
        // On s'assure que le dossier de test existe
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        file_put_contents($filePath, 'contenu factice');

        $this->assertFileExists($filePath);
        
        // Nettoyage après le test
        unlink($filePath);
    }

    public function testCreationMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = new Utilisateur();
        $user->setEmail('min@test.fr')
            ->setPseudo('MinUser')
            ->setMdp('password')
            ->setAccordGdpr(true) // Vrai booléen
            ->setIsModo(false);

        $entityManager->persist($user);
        $entityManager->flush();

        // Vérification de l'instanciation et des types
        $this->assertNotNull($user->getId());
        $this->assertIsInt($user->getId());
        
        // Vérification des valeurs (Est-ce que le string est bien le bon string ?)
        $this->assertEquals('min@test.fr', $user->getEmail());
        $this->assertEquals('MinUser', $user->getPseudo());
        $this->assertFalse($user->isModo());
        $this->assertNull($user->getImageIdentite()); // Doit être nul car non renseigné
    }

    public function testCreationMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = new Utilisateur();
        $email = 'max@test.fr';
        $pseudo = 'MaxUser';
        $image = 'photo.jpg';

        $user->setEmail($email)
            ->setPseudo($pseudo)
            ->setMdp('secret')
            ->setAccordGdpr(true)
            ->setIsModo(true)
            ->setImageIdentite($image);

        $entityManager->persist($user);
        $entityManager->flush();

        // Vérification complète des données
        $this->assertEquals($email, $user->getEmail());
        $this->assertIsString($user->getEmail());
        
        $this->assertEquals($pseudo, $user->getPseudo());
        
        $this->assertEquals($image, $user->getImageIdentite());
        $this->assertIsString($user->getImageIdentite());

        // Test de la logique métier (Roles)
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }


}
