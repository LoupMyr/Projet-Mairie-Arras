<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Identite;
use App\Form\IdentiteType;
use App\Entity\Fichier;
use App\Form\FichierType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Controller\User;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class IdentiteController extends AbstractController
{
    #[Route('/private-liste-identite', name: 'liste-identite')]
    public function listeIdentite(): Response
    {
        $repoIdentite = $this->getDoctrine()->getRepository(Identite::class);
        $repoFichier = $this->getDoctrine()->getRepository(Fichier::class);
        $identite = $repoIdentite->findAll();
        $fichier = $repoFichier->findAll();
        return $this->render('identite/liste-identite.html.twig', [
           'identite' => $identite,
           'fichier' => $fichier
        ]);
    }

    #[Route('/private-telechargement-fichier/{id}', name: 'telechargement-fichier', requirements: ["id"=>"\d+"] )]    
    public function telechargementFichier(int $id) {        
        $doctrine = $this->getDoctrine();        
        $repoIdentite = $doctrine->getRepository(Identite::class);         
        $fichier = $repoIdentite->find($domicile_id);        
        if ($fichier == null){            
            $this->redirectToRoute('liste-identite'); }        
            else{            
                return $this->file($this->getParameter('file_directory').'/'.$fichier->getNomServeur(), $fichier->getNomOriginal());        
            }
        }

        #[Route('/private-accepter-demande/{id}', name: 'accepter-demande', requirements: ["id"=>"\d+"] )] 
        public function accepterDemande(MailerInterface $mailer): Response {
            $email = (new TemplatedEmail())
            ->from('loupmayeur2003@gmail.com')
            ->to($this->getUser()->getEmail())
            ->subject("Votre dossier a été accepté")
            ->htmlTemplate('emails/emailDossier.html.twig')
            ->context([

                ]);
            $mailer->send($email);
            $this->addFlash('notice','Notification envoyé (demande prise en charge)');
            return $this -> redirectToRoute('liste-identite');
        }


        #[Route('/private-carte-recu/{id}', name: 'carte-recu', requirements: ["id"=>"\d+"] )] 
        public function carteRecu(MailerInterface $mailer): Response {
            $email = (new TemplatedEmail())
            ->from('loupmayeur2003@gmail.com')            
            ->to($this->getUser()->getEmail())
            ->subject("Votre nouvelle carte d'identitée est disponible")
            ->htmlTemplate('emails/emailCarte.html.twig')
            ->context([

                ]);
            $mailer->send($email);
            $this->addFlash('notice','Notification envoyé (carte dispo)');
            return $this -> redirectToRoute('liste-identite');
        }
}
