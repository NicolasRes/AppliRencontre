<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 40)]
    private ?string $mdp = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $imageIdentite = null;

    #[ORM\Column]
    private ?bool $accordGdpr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getImageIdentite(): ?string
    {
        return $this->imageIdentite;
    }

    public function setImageIdentite(?string $imageIdentite): static
    {
        $this->imageIdentite = $imageIdentite;

        return $this;
    }

    public function isAccordGdpr(): ?bool
    {
        return $this->accordGdpr;
    }

    public function setAccordGdpr(bool $accordGdpr): static
    {
        $this->accordGdpr = $accordGdpr;

        return $this;
    }
}
