<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Notif;

class ProfilController extends AbstractController
{
    #[Route('/profil-profil', name: 'profil')]
    public function profil(): Response
    {
        $repoNotif = $this->getDoctrine()->getRepository(Notif::class);
        $notifs = $repoNotif->findAll();
        return $this->render('profil/profil.html.twig', [
            'notifs' => $notifs
        ]);
    }
}
