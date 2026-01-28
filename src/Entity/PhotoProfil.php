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
}
