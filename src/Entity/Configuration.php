<?php

namespace App\Entity;

use App\Repository\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $ageMin = null;

    #[ORM\Column]
    private ?int $ageMax = null;

    #[ORM\Column]
    private ?int $rayon = null;

    #[ORM\Column]
    private array $genresVisibles = [];

    #[ORM\Column]
    private ?bool $etatNotif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgeMin(): ?int
    {
        return $this->ageMin;
    }

    public function setAgeMin(int $ageMin): static
    {
        $this->ageMin = $ageMin;

        return $this;
    }

    public function getAgeMax(): ?int
    {
        return $this->ageMax;
    }

    public function setAgeMax(int $ageMax): static
    {
        $this->ageMax = $ageMax;

        return $this;
    }

    public function getRayon(): ?int
    {
        return $this->rayon;
    }

    public function setRayon(int $rayon): static
    {
        $this->rayon = $rayon;

        return $this;
    }

    public function getGenresVisibles(): array
    {
        return $this->genresVisibles;
    }

    public function setGenresVisibles(array $genresVisibles): static
    {
        $this->genresVisibles = $genresVisibles;

        return $this;
    }

    public function isEtatNotif(): ?bool
    {
        return $this->etatNotif;
    }

    public function setEtatNotif(bool $etatNotif): static
    {
        $this->etatNotif = $etatNotif;

        return $this;
    }
}
