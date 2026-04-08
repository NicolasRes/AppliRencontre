<?php

namespace App\Tests;

use App\Entity\Conversation;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConversationTest extends KernelTestCase
{
    public function testCreationConversation(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de deux participants
        $user1 = new Utilisateur();
        $user1->setPseudo('UserAlpha')
              ->setEmail('alpha@test.com')
              ->setMdp('password123')
              ->setIsModo(false);
        $entityManager->persist($user1);

        $user2 = new Utilisateur();
        $user2->setPseudo('UserBeta')
              ->setEmail('beta@test.com')
              ->setMdp('password123')
              ->setIsModo(false);
        $entityManager->persist($user2);

        // 2. Création de la conversation
        $conversation = new Conversation();
        $conversation->addParticipant($user1);
        $conversation->addParticipant($user2);

        $entityManager->persist($conversation);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($conversation->getId());
        $this->assertCount(2, $conversation->getParticipants());
        $this->assertTrue($conversation->getParticipants()->contains($user1));
        $this->assertTrue($conversation->getParticipants()->contains($user2));
    }
}