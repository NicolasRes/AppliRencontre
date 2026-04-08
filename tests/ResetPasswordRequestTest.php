<?php

namespace App\Tests;

use App\Entity\ResetPasswordRequest;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResetPasswordRequestTest extends KernelTestCase
{
    /**
     * Test de création d'une demande de réinitialisation de mot de passe.
     */
    public function testCreationResetPasswordRequest(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création et persistance de l'Utilisateur
        // ResetPasswordRequest n'a pas de cascade persist, l'user doit exister en base.
        $user = new Utilisateur();
        $user->setPseudo('UserReset')
             ->setEmail('reset@test.com')
             ->setMdp('password123')
             ->setIsModo(false);
        
        $entityManager->persist($user);

        // 2. Préparation des données pour le constructeur
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $selector = 'a1b2c3d4e5f6';
        $hashedToken = 'hashed_token_example';

        // 3. Création de la requête
        $resetRequest = new ResetPasswordRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );

        $entityManager->persist($resetRequest);
        $entityManager->flush();

        // 4. Assertions
        $this->assertNotNull($resetRequest->getId());
        
        // Vérification de l'utilisateur lié
        $this->assertSame($user, $resetRequest->getUser());
        $this->assertEquals('reset@test.com', $resetRequest->getUser()->getEmail());

        // Vérification des données issues du Trait (fourni par le bundle)
        $this->assertEquals($expiresAt, $resetRequest->getExpiresAt());
        $this->assertEquals($hashedToken, $resetRequest->getHashedToken());
        
        // Vérifie que la date de demande a été initialisée (automatique via le trait)
        $this->assertInstanceOf(\DateTimeInterface::class, $resetRequest->getRequestedAt());
    }
}