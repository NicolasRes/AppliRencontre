<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SignalementRepository::class)]
class Signalement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateS = null;

    #[ORM\Column]
    private ?int $statut = null;

    #[ORM\Column(length: 50)]
    private ?string $motif = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $auteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $cible = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateS(): ?\DateTime
    {
        return $this->dateS;
    }

    public function setDateS(\DateTime $dateS): static
    {
        $this->dateS = $dateS;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    public function setAuteur(?Utilisateur $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getCible(): ?Utilisateur
    {
        return $this->cible;
    }

    public function setCible(?Utilisateur $cible): static
    {
        $this->cible = $cible;

        return $this;
    }
}
