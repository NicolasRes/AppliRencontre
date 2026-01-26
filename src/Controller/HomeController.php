<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return new Response('<h1>Bienvenue sur ton site Symfony !</h1>');
    }
}
