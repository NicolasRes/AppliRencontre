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

    /**
     * Méthode qui renvoie les utilisateurs avec lesquels l'utilisateur courant a un match
     * @param Utilisateur $currentUser Utilisateur courant
     * @return array Utilisateurs
     */
    public function findMatchedUsers(Utilisateur $currentUser): array
    {
        return $this->createQueryBuilder('u')
            // On fait une jointure sur l'entité Rencontre où l'utilisateur courant et l'utilisateur 'u' sont impliqués
            ->innerJoin(
                \App\Entity\Rencontre::class, 
                'r', 
                'WITH', 
                '(r.utilisateur = :user AND r.utilisateur2 = u) OR (r.utilisateur = u AND r.utilisateur2 = :user)'
            )
            ->andWhere('r.statut = 2') // si le match est validé, statut 2
            ->setParameter('user', $currentUser)
            ->orderBy('u.pseudo', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
