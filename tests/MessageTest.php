<?php

namespace App\Tests;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{
    public function testCreationMessage(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'auteur
        $auteur = new Utilisateur();
        $auteur->setPseudo('AuteurMsg')
               ->setEmail('auteur.msg@test.com')
               ->setMdp('password123')
               ->setIsModo(false);
        $entityManager->persist($auteur);

        // 2. Création de la conversation
        // (On suppose que l'entité Conversation n'a pas de champs obligatoires complexes au constructeur)
        $conversation = new Conversation();
        $conversation->addParticipant($auteur);
        $entityManager->persist($conversation);

        // 3. Création du Message
        $message = new Message();
        $createdAt = new \DateTimeImmutable();
        
        $message->setContent('Ceci est un message de test.')
                ->setCreatedAt($createdAt)
                ->setAuthor($auteur)
                ->setConversation($conversation);

        $entityManager->persist($message);
        $entityManager->flush();

        // 4. Assertions
        $this->assertNotNull($message->getId());
        $this->assertEquals('Ceci est un message de test.', $message->getContent());
        $this->assertEquals($createdAt, $message->getCreatedAt());
        
        // Vérification des relations
        $this->assertSame($auteur, $message->getAuthor());
        $this->assertSame($conversation, $message->getConversation());
    }
}