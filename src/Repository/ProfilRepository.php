<?php

namespace App\Repository;

use App\Entity\Configuration;
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
    public function findProfilsNonSwipes(Configuration $config, Utilisateur $user): array
    {   
        $qb = $this->createQueryBuilder('p')
            // Exclut les profils déjà swipés par cet utilisateur
            ->leftJoin('App\Entity\Rencontre', 'r', 'WITH', 'r.utilisateur2 = p.utilisateur AND r.utilisateur = :user')
            ->where('p.utilisateur != :user') // On exclut l'utilisateur lui-même
            ->andWhere('r.id IS NULL')        // Seulement ceux qui n'ont pas de ligne dans Rencontre
            ->setParameter('user', $user);

        // Si l'utilisateur a une configuration, on applique les filtres
        if ($config) {
            
            // Filtre : Âge Minimum
            if ($config->getAgeMin() !== null) {
                $qb->andWhere('p.age >= :ageMin')
                   ->setParameter('ageMin', $config->getAgeMin());
            }

            // Filtre : Âge Maximum
            if ($config->getAgeMax() !== null) {
                $qb->andWhere('p.age <= :ageMax')
                   ->setParameter('ageMax', $config->getAgeMax());
            }

            // Filtre : Genres Visibles
            $genres = $config->getGenresVisibles();
            if (!empty($genres)) {
                // Doctrine gère automatiquement les tableaux avec l'opérateur IN
                $qb->andWhere('p.genre IN (:genres)')
                   ->setParameter('genres', $genres);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Méthode qui permet une recherche de profil filtrée
     * @param string|null $pseudo Le nom / prénom de l'utilisateur
     * @param string|null $genre Le genre de l'utilisateur
     * @param int|null $ageMin L'âge minimum recherché
     * @param int|null $ageMax L'âge maximal recherché
     * @return array La liste des utilisateurs qui répondent aux critères de recherche
     */
    public function searchProfiles(
        ?string $pseudo,    // Le "?" devant le type signifie : string OU null
        ?string $genre,
        ?int $ageMin,
        ?int $ageMax
    ): array {

        $qb = $this->createQueryBuilder('p');   // p pour table Profil

        if($pseudo) {
            $qb->andWhere('p.nom LIKE :pseudo OR p.prenom LIKE :pseudo')    // On cherche un profil dont le nom ou prénom commence par la recherche
                ->setParameter('pseudo', '%' . $pseudo . '%');
        }

        if($genre) {
            $qb->andWhere('p.genre = :genre')   // Filtre un genre précis
                ->setParameter('genre', $genre);
        }

        if($ageMin !== null) {
            $qb->andWhere('p.age >= :ageMin')   // Pas de recherche d'âge < 18 autorisé, si val < 18 envoyée -> on force l'âge min à 18
                ->setParameter('ageMin', max(18, $ageMin));
        }

        if($ageMax !== null) {
            $qb->andWhere('p.age <= :ageMax')   //
                ->setParameter('ageMax', max(18, min(113, $ageMax)));   // + grande valeur entre 18 et la valeur entrée (113 max -> doyen de l'humanité)
        }

        return $qb
            ->setMaxResults(30) // Affichage de 30 résultats max
            ->getQuery()
            ->getResult();
    }
}
