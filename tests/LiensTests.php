<?php

namespace App\Tests;

use App\Entity\Liens;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LiensTest extends KernelTestCase
{
    public function testCreationLien(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création de l'utilisateur lié
        $user = new Utilisateur();
        $user->setPseudo('UserLien')
             ->setEmail('lien.test@test.com')
             ->setMdp('password123')
             ->setIsModo(false);
        $entityManager->persist($user);

        // 2. Création de l'entité Liens
        $lien = new Liens();
        $expiration = new \DateTime('+24 hours');
        
        $lien->setExpDate($expiration)
             ->setUtilise(false)
             ->setUtilisateur($user);

        $entityManager->persist($lien);
        $entityManager->flush();

        // 3. Assertions
        $this->assertNotNull($lien->getId());
        $this->assertFalse($lien->isUtilise());
        
        // On compare les dates (format Y-m-d car c'est souvent stocké en DATE simple)
        $this->assertEquals(
            $expiration->format('Y-m-d'), 
            $lien->getExpDate()->format('Y-m-d')
        );

        // Vérification de la relation
        $this->assertSame($user, $lien->getUtilisateur());
    }
}