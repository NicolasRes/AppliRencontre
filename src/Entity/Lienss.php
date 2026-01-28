<?php

namespace App\Entity;

use App\Repository\LienssRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LienssRepository::class)]
class Lienss
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $expDate = null;

    #[ORM\Column]
    private ?bool $utilise = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpDate(): ?\DateTime
    {
        return $this->expDate;
    }

    public function setExpDate(\DateTime $expDate): static
    {
        $this->expDate = $expDate;

        return $this;
    }

    public function isUtilise(): ?bool
    {
        return $this->utilise;
    }

    public function setUtilise(bool $utilise): static
    {
        $this->utilise = $utilise;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
