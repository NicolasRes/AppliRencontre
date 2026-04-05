<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Utilisateur;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConversationRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Conversation::class);
    }

    public function findByUsers(Utilisateur $sender, Utilisateur $recipient): ?Conversation
    {
        return $this->createQueryBuilder('c')
        ->where(':sender MEMBER OF c.participants')
        ->andWhere(':recipient MEMBER OF c.participants')
        ->setParameter('sender', $sender)
        ->setParameter('recipient', $recipient)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function save(Conversation $conversation):void {
        $this->getEntityManager()->persist($conversation);
        $this->getEntityManager()->flush();
    }
}

