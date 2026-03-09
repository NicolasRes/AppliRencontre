<?php

namespace App\Tests;

use App\Entity\Signalement;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SignalementTest extends KernelTestCase
{

    public function testCreationSignalementMinimal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $auteur = (new Utilisateur())
            ->setEmail('auteur.min@test.fr')
            ->setPseudo('Auteur')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);

        $cible = (new Utilisateur())
            ->setEmail('cible.min@test.fr')
            ->setPseudo('Cible')
            ->setMdp('password')
            ->setAccordGdpr(true)
            ->setIsModo(false);

        $entityManager->persist($auteur);
        $entityManager->persist($cible);

        $signalement = new Signalement();
        $signalement->setAuteur($auteur)
                    ->setCible($cible)
                    ->setDateS(new \DateTime())
                    ->setMotif('Motif minimal')
                    ->setStatut(0);

        $entityManager->persist($signalement);
        $entityManager->flush();

        $this->assertNotNull($signalement->getId());
        $this->assertIsInt($signalement->getId());
        $this->assertInstanceOf(Utilisateur::class, $signalement->getAuteur());
        $this->assertInstanceOf(\DateTimeInterface::class, $signalement->getDateS());
    }

    public function testCreationSignalementMaximal(): void
    {
        self::bootKernel();
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $auteur = (new Utilisateur())->setEmail('auteur.max@test.fr')->setPseudo('A')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $cible = (new Utilisateur())->setEmail('cible.max@test.fr')->setPseudo('C')->setMdp('p')->setAccordGdpr(true)->setIsModo(false);
        $date = new \DateTime('2023-12-25');
        $motif = "Comportement inapproprié durant la rencontre";
        $statut = 2;

        $entityManager->persist($auteur);
        $entityManager->persist($cible);

        $signalement = new Signalement();
        $signalement->setAuteur($auteur)
                    ->setCible($cible)
                    ->setDateS($date)
                    ->setMotif($motif)
                    ->setStatut($statut);

        $entityManager->persist($signalement);
        $entityManager->flush();

        $this->assertEquals($motif, $signalement->getMotif());
        $this->assertIsString($signalement->getMotif());
        
        $this->assertEquals($statut, $signalement->getStatut());
        $this->assertIsInt($signalement->getStatut());

        $this->assertEquals($date->format('Y-m-d'), $signalement->getDateS()->format('Y-m-d'));
        
        $this->assertEquals($auteur->getEmail(), $signalement->getAuteur()->getEmail());
        $this->assertEquals($cible->getEmail(), $signalement->getCible()->getEmail());
    }
}