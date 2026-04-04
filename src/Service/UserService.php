<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;

class UserService {
    public function __construct(
        private readonly UtilisateurRepository $utilisateurRepository
    ) {}

    /**
     * @return Utilisateur[]
     */
    public function findAll() : array {
        return $this->utilisateurRepository->findBy([], ['username' => 'ASC']);
    }
}
