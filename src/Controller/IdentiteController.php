<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Identite;
use App\Form\IdentiteType;
use App\Entity\Fichier;
use App\Form\FichierType;
use App\Entity\User;
use App\Entity\Notif;


class IdentiteController extends AbstractController
{
    #[Route('/private-liste-identite', name: 'liste-identite')]
    public function listeIdentite(): Response
    {
        $repoIdentite = $this->getDoctrine()->getRepository(Identite::class);
        $identite = $repoIdentite->findAll();
        return $this->render('identite/liste-identite.html.twig', [
           'identites' => $identite,
        ]);
    }

    #[Route('/private-telechargement-fichier/{id}', name: 'telechargement-fichier', requirements: ["id"=>"\d+"] )]    
    public function telechargementFichier(int $id): Response {        
        $doctrine = $this->getDoctrine();        
        $repoFichier = $doctrine->getRepository(Fichier::class);         
        $fichier = $repoFichier->find($id);
        if ($fichier == null){            
            $this->redirectToRoute('liste-identite'); }        
            else{            
                return $this->file($this->getParameter('file_directory').'/'.$fichier->getNomServeur(), $fichier->getNomOriginal());        
            }
    }

    #[Route('/private-accepter-demande/{id}', name: 'accepter-demande', requirements: ["id"=>"\d+"] )] 
    public function accepterDemande(MailerInterface $mailer, String $id): Response {
        $doctrine = $this->getDoctrine();
        $repoIdentite = $doctrine->getRepository(Identite::class);
        $demande = $repoIdentite->find($id);
        $mail = (new TemplatedEmail())
        ->from('loupmayeur2003@gmail.com')
        ->to($demande->getEmail())
        ->subject("Votre dossier a été accepté")
        ->htmlTemplate('emails/emailDossier.html.twig')
        ->context([

            ]);
        
        $notif = new Notif();
        $notif -> setMessage('Votre demande a été accepté');
        $notif -> setType(1);
        $notif -> setUser($demande);
        $em = $this->getDoctrine()->getManager();
        $em->persist($notif);
        $em->flush();

        $mailer->send($mail);
        $this->addFlash('notice','Notification envoyé, demande prise en charge');
        return $this -> redirectToRoute('liste-identite');
    }

    #[Route('/private-carte-recu/{id}', name: 'carte-recu', requirements: ["id"=>"\d+"] )] 
        public function carteRecu(MailerInterface $mailer, int $id): Response {
        $demande = $this->getDoctrine()->getRepository(Identite::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $mail = (new TemplatedEmail())
        ->from('loupmayeur2003@gmail.com')            
        ->to($demande->getEmail())
        ->subject("Votre nouvelle carte d'identitée est disponible")
        ->htmlTemplate('emails/emailCarte.html.twig')
        ->context([

            ]);

        $notif = new Notif();
        $notif -> setMessage('Votre demande de carte d\'identité vous attend à la mairie d\Arras !');
        $notif -> setUser($demande);
        $demande -> setTerminer(true);
        $notif -> setType(2);
        $em->persist($demande);
        $em->persist($notif);
        $em->flush();

        $mailer->send($mail);
        $domicile = $demande->getDomicile();
        $carte = $demande->getCarte();
        
        if($domicile != null && $carte != null){
            try {
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$domicile->getNomServeur())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$domicile->getNomServeur());
                }
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$carte->getNomServeur())){
                        $filesystem->remove($this->getParameter('file_directory').'/'.$carte->getNomServeur());
                }
            }
            catch (IOExceptionInterface $exception) {
                $this->addFlash('notice', 'Erreur');
            }
            $demande->setDomicile(null);
            $demande->setCarte(null);
            $em -> persist($demande);
            /*$em -> remove($domicile);
            $em -> remove($carte);*/
            $em -> flush();
        }
        $this->addFlash('notice','Notification envoyé, carte dispo');

        return $this -> redirectToRoute('liste-identite');
    }

    #[Route('/private-supprimer-demande/{id}', name: 'supprimer-demande', requirements: ["id"=>"\d+"] )] 
    public function supprimerDemande(MailerInterface $mailer, int $id): Response {
        $demande = $this->getDoctrine()->getRepository(Identite::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $mail = (new TemplatedEmail())
        ->from('loupmayeur2003@gmail.com')
        ->to($demande->getEmail())
        ->subject("Votre demande de nouvelle carte d'identité à été rejeté")
        ->htmlTemplate('emails/emailRefuse.html.twig')
        ->context([

            ]);

        $notif = new Notif();
        $notif -> setMessage('Votre demande de carte d\'identité a été rejeté.');
        $notif -> setUser($demande);
        $demande -> setTerminer(true);
        $notif -> setType(3);
        $em->persist($demande);
        $em -> persist($notif);
        $em -> flush();
        $mailer->send($mail);

        $domicile = $demande->getDomicile();
        $carte = $demande->getCarte();
        if($domicile != null && $carte != null){
            try {
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$domicile->getNomServeur())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$domicile->getNomServeur());
                }
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$carte->getNomServeur())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$carte->getNomServeur());
                }
            }
            catch (IOExceptionInterface $exception) {
                $this->addFlash('notice', 'Erreur');
            }
            $demande->setDomicile(false);
            $demande->setCarte(false);
            $em -> remove($domicile);
            $em -> remove($carte);
            $em -> flush();
        }
        $this->addFlash('notice','Notification envoyé, demande rejeté');
        return $this -> redirectToRoute('liste-identite');
    }
    
}
