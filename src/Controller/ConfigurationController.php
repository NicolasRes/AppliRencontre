<?php

namespace App\Controller;

use App\Entity\Configuration;
use App\Form\ConfigurationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class ConfigurationController extends AbstractController
{
    #[Route('/configuration', name: 'app_configuration')]
    public function index(EntityManagerInterface $em, Request $request): Response {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $configUser = $em->getRepository(Configuration::class)->findOneBy(['utilisateur' => $user]);
        $form = $this->createForm(ConfigurationType::class, $configUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($configUser);
            $em->flush();
            return $this->redirectToRoute('app_home_page');
        }
        return $this->render('configuration/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
