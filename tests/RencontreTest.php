<?php

namespace App\Tests;

use App\Entity\Rencontre;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RencontreTest extends KernelTestCase
{
    /**
     * Test de création avec le strict minimum requis (champs non nullables).
     */
    public function testCreationRencontreMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Création des deux utilisateurs obligatoires
        $user1 = (new Utilisateur())->setEmail('user1.min@test.fr')->setPseudo('U1')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $user2 = (new Utilisateur())->setEmail('user2.min@test.fr')->setPseudo('U2')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($user1);
        $entityManager->persist($user2);

        $rencontre = new Rencontre();
        $rencontre->setUtilisateur($user1)
                  ->setUtilisateur2($user2)
                  ->setStatut(0)
                  ->setDateCreation(new \DateTime());

        $entityManager->persist($rencontre);
        $entityManager->flush();

        // Assertions
        $this->assertNotNull($rencontre->getId());
        $this->assertIsInt($rencontre->getId());
        $this->assertEquals($user1, $rencontre->getUtilisateur());
    }

    /**
     * Test de création maximale vérifiant l'exactitude de toutes les données.
     */
    public function testCreationRencontreMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // Préparation des données
        $user1 = (new Utilisateur())->setEmail('user1.max@test.fr')->setPseudo('Max1')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $user2 = (new Utilisateur())->setEmail('user2.max@test.fr')->setPseudo('Max2')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($user1);
        $entityManager->persist($user2);

        $date = new \DateTime('2024-02-19 14:00:00');
        $statut = 2;

        $rencontre = new Rencontre();
        $rencontre->setUtilisateur($user1)
                  ->setUtilisateur2($user2)
                  ->setStatut($statut)
                  ->setDateCreation($date);

        $entityManager->persist($rencontre);
        $entityManager->flush();

        // Vérification de l'intégrité des données
        $this->assertEquals($statut, $rencontre->getStatut());
        $this->assertIsInt($rencontre->getStatut());
        
        // Vérification du format de date pour éviter les décalages de secondes
        $this->assertEquals($date->format('Y-m-d H:i'), $rencontre->getDateCreation()->format('Y-m-d H:i'));
        
        // Vérification des relations
        $this->assertEquals($user1->getEmail(), $rencontre->getUtilisateur()->getEmail());
        $this->assertEquals($user2->getEmail(), $rencontre->getUtilisateur2()->getEmail());
    }
}