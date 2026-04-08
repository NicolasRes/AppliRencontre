<?php

namespace App\Tests;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtilisateurTest extends KernelTestCase
{
    /**
     * Test Minimal : Vérifie que l'entité peut être créée avec ses champs obligatoires.
     */
    public function testCreationUtilisateurMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $utilisateur = new Utilisateur();
        $utilisateur->setPseudo('JohnDoe')
                    ->setEmail('john.doe@test.com')
                    ->setMdp('password123')
                    ->setIsModo(false);
        // On ne set pas le statut, on veut vérifier qu'il prend bien la valeur par défaut

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $this->assertNotNull($utilisateur->getId());
        $this->assertEquals('JohnDoe', $utilisateur->getPseudo());
        $this->assertNull($utilisateur->getImageIdentite()); // Champ nullable
        
        // Vérification de la valeur par défaut du statut
        $this->assertEquals(Utilisateur::STATUS_PENDING, $utilisateur->getStatus());
        $this->assertTrue($utilisateur->isPending());
        $this->assertFalse($utilisateur->isApproved());
    }

    /**
     * Test Maximal : Vérifie l'intégrité de tous les champs, y compris les champs optionnels.
     */
    public function testCreationUtilisateurMaximal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $utilisateur = new Utilisateur();
        $utilisateur->setPseudo('JaneDoe')
                    ->setEmail('jane.doe@test.com') // Email différent pour éviter les conflits
                    ->setMdp('securepassword456')
                    ->setIsModo(true)
                    ->setImageIdentite('carte_id.png')
                    ->setStatus(Utilisateur::STATUS_APPROVED);

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $this->assertNotNull($utilisateur->getId());
        $this->assertEquals('carte_id.png', $utilisateur->getImageIdentite());
        $this->assertTrue($utilisateur->isApproved());
        $this->assertFalse($utilisateur->isPending());
    }

    /**
     * Test de l'exception : Vérifie que la modification avec un statut invalide plante bien.
     */
    public function testStatutInvalideDeclencheException(): void
    {
        $utilisateur = new Utilisateur();
        
        // On dit à PHPUnit qu'on s'attend à recevoir cette exception précise
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Statut invalide : status_inexistant');

        // L'appel qui doit déclencher l'erreur
        $utilisateur->setStatus('status_inexistant');
    }

    /**
     * Test des rôles : Vérifie la logique de la méthode getRoles() selon isModo.
     */
    public function testRolesUtilisateur(): void
    {
        $utilisateur = new Utilisateur();
        
        // Cas 1 : Utilisateur normal
        $utilisateur->setIsModo(false);
        $roles = $utilisateur->getRoles();
        $this->assertContains('ROLE_USER', $roles);
        $this->assertNotContains('ROLE_ADMIN', $roles);

        // Cas 2 : Utilisateur modérateur
        $utilisateur->setIsModo(true);
        $rolesAdmin = $utilisateur->getRoles();
        $this->assertContains('ROLE_USER', $rolesAdmin);
        $this->assertContains('ROLE_ADMIN', $rolesAdmin);
    }
}