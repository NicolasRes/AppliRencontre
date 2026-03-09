<?php

namespace App\Tests;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationTest extends KernelTestCase
{
    /**
     * Test minimal : Uniquement les champs obligatoires.
     */
    public function testCreationNotificationMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur lié (obligatoire)
        $user = (new Utilisateur())
            ->setEmail('notif.min@test.fr')
            ->setPseudo('UserNotif')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);
        
        $entityManager->persist($user);

        // 2. Création de la notification avec les champs obligatoires
        $notification = new Notification();
        $notification->setContenu("Nouvelle alerte")
                     ->setType(1)
                     ->setLu(false)
                     ->setUtilisateur($user);

        $entityManager->persist($notification);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($notification->getId());
        $this->assertIsInt($notification->getId());
        $this->assertFalse($notification->isLu());
        $this->assertEquals($user, $notification->getUtilisateur());
    }

    /**
     * Test maximal : Vérification complète des données et types.
     */
    public function testCreationNotificationMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = (new Utilisateur())
            ->setEmail('notif.max@test.fr')
            ->setPseudo('MaxNotif')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);
        
        $entityManager->persist($user);

        $contenu = "Votre profil a été consulté par un autre membre !";
        $type = 99;

        $notification = new Notification();
        $notification->setContenu($contenu)
                     ->setType($type)
                     ->setLu(true)
                     ->setUtilisateur($user);

        $entityManager->persist($notification);
        $entityManager->flush();

        // Vérifications de l'intégrité
        $this->assertEquals($contenu, $notification->getContenu());
        $this->assertIsString($notification->getContenu());
        
        $this->assertEquals($type, $notification->getType());
        $this->assertIsInt($notification->getType());
        
        $this->assertTrue($notification->isLu());
        $this->assertIsBool($notification->isLu());

        // Vérification du lien utilisateur
        $this->assertEquals($user->getEmail(), $notification->getUtilisateur()->getEmail());
    }
}