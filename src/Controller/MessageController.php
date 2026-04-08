<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Factory\MessageFactory;
use App\Service\TopicService;
use App\DTO\CreateMessage;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface; // Import indispensable pour le flush

final class MessageController extends AbstractController{
    public function __construct(
        private ConversationRepository $conversationRepository, 
        private readonly HubInterface $hub, 
        private readonly MessageFactory $factory, 
        private readonly TopicService $topicService,
        private readonly EntityManagerInterface $em // On injecte l'EntityManager
    ) {
    }

    #[Route('/messages', name: 'message.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateMessage $payload): Response {
        // 1. On récupère l'utilisateur actuellement connecté (l'auteur légitime)
        /** @var Utilisateur $author */
        $author = $this->getUser();

        // 2. On récupère la conversation
        $conversation = $this->conversationRepository->find($payload->conversationId);

        // 3. On utilise $author (notre variable sécurisée) au lieu de $payload->author
        $message = $this->factory->create(
            conversation: $conversation,
            author: $author,
            content: $payload->content
        );

        // --- CORRECTION : Sauvegarde en base de données ---
        // Sans ces deux lignes, le message n'est pas enregistré immédiatement,
        // ce qui explique pourquoi il n'apparaît pas au refresh ou avec retard.
        $this->em->persist($message);
        $this->em->flush(); 
        // --------------------------------------------------

        // 4. On envoie la notification Mercure
        $data = [
            'authorId' => $message->getAuthor()->getId(),
            'content'  => $message->getContent()
        ];
        $update = new Update(
            topics: $this->topicService->getTopicUrl($conversation),
            data: json_encode($data),
            private: true
        );
        $this->hub->publish($update);

        return new Response('', Response::HTTP_CREATED);
    }
}