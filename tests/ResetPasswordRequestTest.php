<?php

namespace App\Tests;

use App\Entity\ResetPasswordRequest;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResetPasswordRequestTest extends KernelTestCase
{
    /**
     * Test de création minimale avec les paramètres obligatoires du constructeur.
     */
    public function testCreationResetRequestMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur obligatoire
        $user = (new Utilisateur())
            ->setEmail('reset.min@test.fr')
            ->setPseudo('UserReset')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);

        $entityManager->persist($user);

        // 2. Création de la demande avec DateTimeImmutable
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $resetRequest = new ResetPasswordRequest(
            $user, 
            $expiresAt, 
            'selector_abc123', 
            'hashed_token_xyz789'
        );

        $entityManager->persist($resetRequest);
        $entityManager->flush();

        // 3. Assertions de base
        $this->assertNotNull($resetRequest->getId());
        $this->assertEquals($user, $resetRequest->getUser());
        $this->assertIsInt($resetRequest->getId());
    }

    /**
     * Test complet vérifiant l'exactitude des données persistées.
     */
    public function testCreationResetRequestMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = (new Utilisateur())->setEmail('reset.max@test.fr')->setPseudo('Max')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($user);

        $expiresAt = new \DateTimeImmutable('+2 hours');
        $selector = 'unique_selector_456';
        $token = 'very_secure_hashed_token';

        $resetRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $token);

        $entityManager->persist($resetRequest);
        $entityManager->flush();

        // Vérification de l'intégrité des données
        $this->assertEquals($user->getEmail(), $resetRequest->getUser()->getEmail());
        $this->assertInstanceOf(ResetPasswordRequest::class, $resetRequest);
        
        // Vérification de la date d'expiration
        $this->assertEquals(
            $expiresAt->format('Y-m-d H:i'), 
            $resetRequest->getExpiresAt()->format('Y-m-d H:i')
        );
    }
}