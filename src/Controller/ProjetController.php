<?php

namespace App\Controller;

use App\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Page publique d'un projet : ses tâches (cahier des charges 7.2).
 * Pour l'instant une simple liste ; elle deviendra un calendrier à l'étape 5.
 */
class ProjetController extends AbstractController
{
    // {id} dans l'URL -> Symfony va chercher le Projet correspondant tout seul (404 s'il n'existe pas)
    #[Route('/projet/{id}', name: 'app_projet_show', requirements: ['id' => '\d+'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }
}
