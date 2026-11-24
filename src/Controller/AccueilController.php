<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Page d'accueil publique (cahier des charges 7.1).
 */
class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('accueil/index.html.twig', [
            'projets' => $projetRepository->findEnCoursAujourdhui(),
        ]);
    }
}
