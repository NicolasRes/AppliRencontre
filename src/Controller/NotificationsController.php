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
        // Vérification de la connexion [cite: 1]
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        
        // On récupère les notifications de l'utilisateur connecté [cite: 1]
        // Ajout d'un tri par ID DESC pour avoir les plus récentes en haut
        $notifs = $em->getRepository(Notification::class)->findBy(
            ['utilisateur' => $user],
            ['id' => 'DESC']
        );

        return $this->render('notifications/index.html.twig', [
            'notifs' => $notifs,
        ]);
    }

    #[Route('/notification/read/{id}', name: 'app_notification_read')]
    public function read(Notification $notif, EntityManagerInterface $em): Response
    {
        // Sécurité : on vérifie que la notification appartient bien à l'utilisateur
        if ($notif->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette notification.');
        }

        $notif->setLu(true); // Marque comme lu [cite: 1]
        $em->flush();

        return $this->redirectToRoute('app_notifications');
    }

    #[Route('/notification/delete/{id}', name: 'app_notification_delete')]
    public function delete(Notification $notif, EntityManagerInterface $em): Response
    {
        // Sécurité : on vérifie que la notification appartient bien à l'utilisateur
        if ($notif->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette notification.');
        }

        $em->remove($notif); // Supprime de la BDD [cite: 1]
        $em->flush();

        return $this->redirectToRoute('app_notifications');
    }
}