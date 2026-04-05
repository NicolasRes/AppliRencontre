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

final class MessageController extends AbstractController{
    public function __construct(private ConversationRepository $conversationRepository, private readonly HubInterface $hub, private readonly MessageFactory $factory, private readonly TopicService $topicService) {

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

        // 4. On envoie la notification Mercure (Le reste de votre code était parfait !)
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
