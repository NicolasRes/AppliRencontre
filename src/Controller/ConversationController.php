<?php

namespace App\Controller;

use App\Factory\ConversationFactory;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConversationRepository;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\HttpFoundation\Request;
use App\Service\TopicService;
use App\Entity\Conversation;

/**
 * @method User|null getUser()
 */
final class ConversationController extends AbstractController {
    public function __construct (private readonly Authorization $authorization, private readonly Discovery $discovery, private readonly ConversationRepository $conversationRepository, private readonly TopicService $topicService, private readonly ConversationFactory $factory) {

    }

    #[Route('/conversation/users/{recipient}', name: 'conversation.show')]
    public function index(?Utilisateur $recipient, Request $request): Response
    {
        /** @var Utilisateur $sender */
        $sender = $this->getUser();
        $conversation = $this->conversationRepository->findByUsers($sender, $recipient);
        if (!$conversation) {
            $conversation = $this->factory->create($sender, $recipient);
        }
        $topic = $this->topicService->getTopicUrl($conversation);
        $this->discovery->addLink($request);
        $this->authorization->setCookie($request, [$topic]);
        return $this->render('conversation/index.html.twig', ['conversation'=>$conversation, 'topic'=>$topic]);
    }

}

?>