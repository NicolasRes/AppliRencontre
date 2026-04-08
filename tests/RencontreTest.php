<?php

namespace App\Tests;

use App\Entity\Rencontre;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RencontreTest extends KernelTestCase
{
    /**
     * Test de création d'une rencontre entre deux utilisateurs.
     */
    public function testCreationRencontre(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création et persistance du premier Utilisateur
        $user1 = new Utilisateur();
        $user1->setPseudo('UserUn')
              ->setEmail('user1@test.com')
              ->setMdp('password123')
              ->setIsModo(false);
        $entityManager->persist($user1);

        // 2. Création et persistance du second Utilisateur
        $user2 = new Utilisateur();
        $user2->setPseudo('UserDeux')
              ->setEmail('user2@test.com')
              ->setMdp('password456')
              ->setIsModo(false);
        $entityManager->persist($user2);

        // 3. Création de la Rencontre
        $rencontre = new Rencontre();
        $date = new \DateTime('now');
        
        $rencontre->setStatut(1) // Par exemple : 1 pour "Matché"
                  ->setDateCreation($date)
                  ->setUtilisateur($user1)
                  ->setUtilisateur2($user2);

        $entityManager->persist($rencontre);
        $entityManager->flush();

        // 4. Assertions
        $this->assertNotNull($rencontre->getId());
        $this->assertEquals(1, $rencontre->getStatut());
        $this->assertEquals($date, $rencontre->getDateCreation());

        // Vérification des relations
        $this->assertSame($user1, $rencontre->getUtilisateur());
        $this->assertSame($user2, $rencontre->getUtilisateur2());
        $this->assertEquals('UserUn', $rencontre->getUtilisateur()->getPseudo());
        $this->assertEquals('UserDeux', $rencontre->getUtilisateur2()->getPseudo());
    }
}