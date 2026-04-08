<?php

namespace App\Tests;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SignalementTest extends KernelTestCase
{
    /**
     * Test complet : Vérifie qu'un signalement est bien créé avec son auteur et sa cible.
     */
    public function testCreationSignalementReussie(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création et persistance de l'Auteur
        $auteur = new Utilisateur();
        $auteur->setPseudo('AuteurSignalement')
               ->setEmail('auteur.sig@test.com')
               ->setMdp('mdp123')
               ->setIsModo(false);
        // Indispensable car Signalement n'a pas cascade: ['persist']
        $entityManager->persist($auteur);

        // 2. Création et persistance de la Cible
        $cible = new Utilisateur();
        $cible->setPseudo('CibleSignalement')
              ->setEmail('cible.sig@test.com')
              ->setMdp('mdp456')
              ->setIsModo(false);
        // Indispensable également
        $entityManager->persist($cible);

        // 3. Création du Signalement
        $signalement = new Signalement();
        $dateSignalement = new \DateTime('now');
        
        $signalement->setDateS($dateSignalement)
                    ->setStatut(0) // Supposons que 0 = en attente
                    ->setMotif('Propos injurieux')
                    ->setAuteur($auteur)
                    ->setCible($cible);

        $entityManager->persist($signalement);
        $entityManager->flush();

        // 4. Assertions (Vérifications)
        $this->assertNotNull($signalement->getId());
        $this->assertEquals($dateSignalement, $signalement->getDateS());
        $this->assertEquals(0, $signalement->getStatut());
        $this->assertEquals('Propos injurieux', $signalement->getMotif());
        
        // Vérification stricte des relations
        $this->assertNotNull($signalement->getAuteur());
        $this->assertNotNull($signalement->getCible());
        $this->assertEquals('AuteurSignalement', $signalement->getAuteur()->getPseudo());
        $this->assertEquals('CibleSignalement', $signalement->getCible()->getPseudo());
    }
}