<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Cantine;

class CantineController extends AbstractController
{
    #[Route('/private-liste-cantine', name: 'liste-cantine')]
    public function listeCantine(): Response
    {
        $repoCantine = $this->getDoctrine()->getRepository(Cantine::class);
        $cantine = $repoCantine->findAll();
        return $this->render('cantine/liste-cantine.html.twig', [
           'cantine' => $cantine
        ]);
    }
}
