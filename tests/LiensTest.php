<?php

namespace App\Tests;

use App\Entity\Liens;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LiensTest extends KernelTestCase
{
    /**
     * Test Minimal : Création avec l'utilisateur et la date obligatoire.
     */
    public function testCreationLiensMinimale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        // 1. Création utilisateur
        $user = (new Utilisateur())->setEmail('lien.min@test.fr')->setPseudo('LienMin')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($user);

        // 2. Création du lien
        $lien = new Liens();
        $lien->setExpDate(new \DateTime('+24 hours'))
             ->setUtilise(false)
             ->setUtilisateur($user);

        $entityManager->persist($lien);
        $entityManager->flush();

        $this->assertNotNull($lien->getId());
        $this->assertFalse($lien->isUtilise());
    }

    /**
     * Test Maximal : Vérification complète des dates et de la relation.
     */
    public function testCreationLiensMaximale(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $user = (new Utilisateur())->setEmail('lien.max@test.fr')->setPseudo('LienMax')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $entityManager->persist($user);

        $dateExp = new \DateTime('2026-12-31 23:59:59');

        $lien = new Liens();
        $lien->setExpDate($dateExp)
             ->setUtilise(true)
             ->setUtilisateur($user);

        $entityManager->persist($lien);
        $entityManager->flush();

        // Vérifications
        $this->assertTrue($lien->isUtilise());
        $this->assertIsBool($lien->isUtilise());

        // Vérification de la date
        $this->assertEquals('2026-12-31', $lien->getExpDate()->format('Y-m-d'));

        // Vérification de l'appartenance
        $this->assertInstanceOf(Utilisateur::class, $lien->getUtilisateur());
        $this->assertEquals('LienMax', $lien->getUtilisateur()->getPseudo());
    }
}
