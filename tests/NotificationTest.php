<?php

namespace App\Tests;

use App\Entity\Notification;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationTest extends KernelTestCase
{
    /**
     * Test de création d'une notification pour un utilisateur.
     */
    public function testCreationNotification(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création et persistance de l'Utilisateur (obligatoire pour le lien)
        $user = new Utilisateur();
        $user->setPseudo('NotifUser')
             ->setEmail('notif.test@test.com')
             ->setMdp('password123')
             ->setIsModo(false);
        $entityManager->persist($user);

        // 2. Création de la Notification
        $notification = new Notification();
        $notification->setContenu('Vous avez reçu un nouveau match !')
                     ->setType(1) // Par exemple : 1 pour les alertes de rencontre
                     ->setLu(false)
                     ->setUtilisateur($user);

        $entityManager->persist($notification);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($notification->getId());
        $this->assertEquals('Vous avez reçu un nouveau match !', $notification->getContenu());
        $this->assertEquals(1, $notification->getType());
        $this->assertFalse($notification->isLu());

        // Vérification du lien avec l'utilisateur
        $this->assertSame($user, $notification->getUtilisateur());
        $this->assertEquals('NotifUser', $notification->getUtilisateur()->getPseudo());
    }
}