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
        $qb = $this->createQueryBuilder('p');

        // On prépare une sous-requête : "Existe-t-il une rencontre où je suis l'auteur et l'autre est la cible ?"
        $subQuery = $this->getEntityManager()->createQueryBuilder()
            ->select('r.id')
            ->from('App\Entity\Rencontre', 'r')
            ->where('r.utilisateur = :user')
            ->andWhere('r.utilisateur2 = p.utilisateur'); // On fait le lien avec le profil p

        return $qb
            // 1. On s'exclut soi-même (on ne veut pas se swiper)
            ->where('p.utilisateur != :user')
            
            // 2. On garde uniquement ceux pour qui la sous-requête ne renvoie RIEN (NOT EXISTS)
            ->andWhere($qb->expr()->not($qb->expr()->exists($subQuery->getDQL())))
            
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
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
