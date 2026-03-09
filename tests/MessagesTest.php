<?php

namespace App\Tests;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Entity\Rencontre;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessagesTest extends KernelTestCase
{
    /**
     * Test Minimal : Uniquement les champs obligatoires (NOT NULL).
     */
    public function testCreationMessageMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'auteur
        $auteur = (new Utilisateur())->setEmail('auteur.msg@test.fr')->setPseudo('Auteur')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($auteur);

        // 2. Création d'un second utilisateur pour la rencontre
        $dest = (new Utilisateur())->setEmail('dest.msg@test.fr')->setPseudo('Dest')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($dest);

        // 3. Création de la rencontre liée
        $rencontre = (new Rencontre())->setUtilisateur($auteur)->setUtilisateur2($dest)->setStatut(1)->setDateCreation(new \DateTime());
        $entityManager->persist($rencontre);

        // 4. Création du message minimal
        $message = new Message();
        $message->setContenu("Hello !")
                ->setTemps(new \DateTime())
                ->setEstLu(false)
                ->setAuteur($auteur)
                ->setRencontre($rencontre);

        $entityManager->persist($message);
        $entityManager->flush();

        // Assertions
        $this->assertNotNull($message->getId());
        $this->assertEquals("Hello !", $message->getContenu());
        $this->assertNull($message->getLienPhoto()); // Optionnel donc doit être null
    }

    /**
     * Test Maximal : Tous les champs, y compris les optionnels, et vérification des types.
     */
    public function testCreationMessageMaximal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Réutilisation simplifiée pour les relations
        $auteur = (new Utilisateur())->setEmail('max.auteur@test.fr')->setPseudo('Max')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $dest = (new Utilisateur())->setEmail('max.dest@test.fr')->setPseudo('Dest')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($auteur);
        $entityManager->persist($dest);

        $rencontre = (new Rencontre())->setUtilisateur($auteur)->setUtilisateur2($dest)->setStatut(1)->setDateCreation(new \DateTime());
        $entityManager->persist($rencontre);

        $date = new \DateTime('2024-02-19 10:00:00');
        $contenu = "Regarde cette photo de ma dernière rencontre !";
        $lienPhoto = "image_message_123.jpg";

        $message = new Message();
        $message->setContenu($contenu)
                ->setTemps($date)
                ->setLienPhoto($lienPhoto)
                ->setEstLu(true)
                ->setAuteur($auteur)
                ->setRencontre($rencontre);

        $entityManager->persist($message);
        $entityManager->flush();

        // Vérifications de l'intégrité
        $this->assertNotNull($message->getId());
        $this->assertEquals($contenu, $message->getContenu());
        $this->assertIsString($message->getContenu());
        
        $this->assertEquals($lienPhoto, $message->getLienPhoto());
        $this->assertTrue($message->isEstLu());
        
        $this->assertEquals($date->format('Y-m-d H:i'), $message->getTemps()->format('Y-m-d H:i'));
        
        // Vérification des relations
        $this->assertInstanceOf(Utilisateur::class, $message->getAuteur());
        $this->assertInstanceOf(Rencontre::class, $message->getRencontre());
    }
}