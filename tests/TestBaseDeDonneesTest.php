<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Utilisateur;

class TestBaseDeDonneesTest extends KernelTestCase{

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

    public function testCreationUtilisateurGenereId(): void
    {
        // 1. On démarre Symfony
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 2. On crée un nouvel utilisateur
        $user = new Utilisateur();
        $user->setEmail('test@equipe6.fr');
        $user->setPseudo('TestPseudo');
        $user->setMdp('TestMdp');
        $user->setAccordGdpr('true');
        $user->setIsModo('false');
        
        
        // Avant la sauvegarde, l'ID doit être nul
        $this->assertNull($user->getId());

        // 3. On enregistre en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // 4. On vérifie que l'ID a bien été généré
        $this->assertNotNull($user->getId(), 'L\'ID devrait être généré par la base de données.');
        $this->assertIsInt($user->getId());
    }


}
