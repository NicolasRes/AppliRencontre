<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 30)]
    private ?string $genre = null;

    #[ORM\Column(length: 50)]
    private ?string $ville = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $presentation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Utilisateur $utilisateur = null;

    /**
     * @var Collection<int, PhotoProfil>
     */
    #[ORM\OneToMany(targetEntity: PhotoProfil::class, mappedBy: 'profil')]
    private Collection $photoProfils;

    public function __construct()
    {
        $this->photoProfils = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): static
    {
        $this->presentation = $presentation;

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

    /**
     * @return Collection<int, PhotoProfil>
     */
    public function getPhotoProfils(): Collection
    {
        return $this->photoProfils;
    }

    public function addPhotoProfil(PhotoProfil $photoProfil): static
    {
        if (!$this->photoProfils->contains($photoProfil)) {
            $this->photoProfils->add($photoProfil);
            $photoProfil->setProfil($this);
        }

        return $this;
    }

    public function removePhotoProfil(PhotoProfil $photoProfil): static
    {
        if ($this->photoProfils->removeElement($photoProfil)) {
            // set the owning side to null (unless already changed)
            if ($photoProfil->getProfil() === $this) {
                $photoProfil->setProfil(null);
            }
        }

        return $this;
    }
}
