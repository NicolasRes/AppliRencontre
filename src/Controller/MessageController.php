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

final class MessageController extends AbstractController {
    public function __construct(
        private ConversationRepository $conversationRepository, 
        private readonly HubInterface $hub, 
        private readonly MessageFactory $factory, 
        private readonly TopicService $topicService,
        private readonly EntityManagerInterface $em // <--- AJOUTE ÇA
    ) {}

    #[Route('/messages', name: 'message.create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateMessage $payload): Response {
        $author = $this->getUser();
        $conversation = $this->conversationRepository->find($payload->conversationId);

        $message = $this->factory->create(
            conversation: $conversation,
            author: $author,
            content: $payload->content
        );

        // --- ÉTAPE CRUCIALE ---
        $this->em->persist($message);
        $this->em->flush(); // On sauve d'abord en base !
        // -----------------------

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
