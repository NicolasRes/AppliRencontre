<?php

namespace App\Controller;

use App\Factory\ConversationFactory;
use App\Entity\Utilisateur;
use App\Entity\Signalement;
use App\Entity\Conversation;
use App\Repository\UtilisateurRepository;
use App\Repository\ConversationRepository;
use App\Service\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;

/**
 * @method Utilisateur|null getUser()
 */
final class ConversationController extends AbstractController {
    public function __construct (private readonly Authorization $authorization,
                                 private readonly Discovery $discovery,
                                 private readonly ConversationRepository $conversationRepository,
                                 private readonly TopicService $topicService,
                                 private readonly ConversationFactory $factory,
                                 private readonly UtilisateurRepository $utilisateurRepository) {

    }

    #[Route('/conversation', name: 'conversation.index')]
    public function list(Request $request): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        $conversations = $user->getConversations();
        $users = $this->utilisateurRepository->findMatchedUsers($user);;

        return $this->render('conversation/chat.html.twig', [
            'users' => $users,
            'conversations' => $conversations,
            'conversation' => null,
        ]);
    }

    #[Route('/conversation/users/{recipient}', name: 'conversation.show')]
    public function index(?Utilisateur $recipient, Request $request): Response
    {
        if (!$recipient) { // Vérification de l'existence du destinataire
            // S'il est introuvable, on redirige vers la liste des conversations
            return $this->redirectToRoute('conversation.index');
        }

        /** @var Utilisateur $sender */
        $sender = $this->getUser();
        
        $users = $this->utilisateurRepository->findMatchedUsers($sender);
        $conversation = $this->conversationRepository->findByUsers($sender, $recipient);

        if (!$conversation) {
            $conversation = $this->factory->create($sender, $recipient);
        }
        
        $conversations = $sender->getConversations();
        $topic = $this->topicService->getTopicUrl($conversation);

        $this->discovery->addLink($request);
        $this->authorization->setCookie($request, [$topic], [], [], null);

        return $this->render('conversation/chat.html.twig', [
            'users' => $users,
            'conversation' => $conversation,
            'topic' => $topic,
            'conversations' => $conversations,
        ]);
    }

    // Notre nouvelle méthode pour gérer le signalement de la conv
    #[Route('/conversation/{id}/signaler', name: 'conversation.signaler', methods: ['POST'])]
    public function signaler(Conversation $conversation, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $motif = $request->request->get('motif');

        // On cherche qui est l'autre personne dans la conv pour savoir qui on signale
        $cible = null;
        foreach ($conversation->getParticipants() as $participant) {
            if ($participant->getId() !== $user->getId()) {
                $cible = $participant;
                break;
            }
        }

        if ($cible && $motif) {
            $sig = new Signalement();
            $sig->setAuteur($user);
            $sig->setCible($cible);
            $sig->setMotif($motif);
            $sig->setDateS(new \DateTime());
            $sig->setStatut(0); // Statut 0 = En attente

            $em->persist($sig);
            $em->flush();

            $this->addFlash('success', 'Signalement envoyé. Les modérateurs vont étudier la conversation.');
        }

        return $this->redirectToRoute('conversation.show', ['recipient' => $cible->getId()]);
    }
}