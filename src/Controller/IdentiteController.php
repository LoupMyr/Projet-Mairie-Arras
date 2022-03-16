<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Identite;

class IdentiteController extends AbstractController
{
    #[Route('/private-liste-identite', name: 'liste-identite')]
    public function listeIdentite(): Response
    {
        $repoIdentite = $this->getDoctrine()->getRepository(Identite::class);
        $identite = $repoIdentite->findAll();
        return $this->render('identite/liste-identite.html.twig', [
           'identite' => $identite
        ]);
    }
}
