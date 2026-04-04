<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Méthode qui renvoie tous les utilisateurs sauf nous même
     * @param Utilisateur $currentUser Utilisateur courant
     * @return array Utilisateurs
     */
    public function findOtherUsers(Utilisateur $currentUser): array
    {
        return $this->createQueryBuilder('u')
            ->where('u != :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->orderBy('u.pseudo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
