<?php

namespace App\Controller;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotificationsController extends AbstractController
{
    #[Route('/notifications', name: 'app_notifications')]
    public function index(EntityManagerInterface $em): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $notifs = null;
        $user = $this->getUser();
        $notifs = $em->getRepository(Notification::class)->findBy(['utilisateur' => $user]);
        return $this->render('notifications/index.html.twig', [
            'notifs' => $notifs,
        ]);
    }
}
