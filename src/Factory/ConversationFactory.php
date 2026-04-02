<?php

namespace App\Factory;

use App\Repository\ConversationRepository;
use App\Entity\Conversation;
use App\Entity\Utilisateur;

class ConversationFactory {
    public function __construct(private readonly ConversationRepository $conversationRepository) {

    }
    public function create(Utilisateur $sender, Utilisateur $recipient): Conversation {
        $conversation = new Conversation();
        $conversation->addParticipant($sender);
        $conversation->addParticipant($recipient);
        $this->conversationRepository->save($conversation);
        return $conversation;
    }
}