<?php

namespace App\Entity;

use App\Repository\PhotoProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoProfilRepository::class)]
class PhotoProfil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $lienPhoto = null;

    #[ORM\ManyToOne(inversedBy: 'photoProfils')]
    private ?Profil $profil = null;

    #[ORM\OneToOne(mappedBy: 'photoProfil', cascade: ['persist', 'remove'])]
    private ?Profil $lienProfil = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLienPhoto(): ?string
    {
        return $this->lienPhoto;
    }

    public function setLienPhoto(string $lienPhoto): static
    {
        $this->lienPhoto = $lienPhoto;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): static
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLienProfil(): ?Profil
    {
        return $this->lienProfil;
    }

    public function setLienProfil(Profil $lienProfil): static
    {
        // set the owning side of the relation if necessary
        if ($lienProfil->getPhotoProfil() !== $this) {
            $lienProfil->setPhotoProfil($this);
        }

        $this->lienProfil = $lienProfil;

        return $this;
    }
}
