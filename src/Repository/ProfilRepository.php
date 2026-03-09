<?php

namespace App\Repository;

use App\Entity\Profil;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profil>
 */
class ProfilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profil::class);
    }

    /**
     * Récupère les profils que l'utilisateur n'a pas encore swipés
     */
    public function findProfilsNonSwipes(Utilisateur $user): array
    {
        return $this->createQueryBuilder('p')
            // On essaie de joindre la table Rencontre uniquement pour l'utilisateur actuel
            ->leftJoin('App\Entity\Rencontre', 'r', 'WITH', 'r.utilisateur2 = p.utilisateur AND r.utilisateur = :user')
            
            // 1. On exclut l'utilisateur connecté lui-même
            ->where('p.utilisateur != :user')
            
            // 2. On ne garde que les profils qui n'ont AUCUNE ligne correspondante dans Rencontre
            // (Si r.id est NULL, c'est que le profil n'a pas été swipé)
            ->andWhere('r.id IS NULL')
            
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}