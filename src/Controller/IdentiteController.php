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


class IdentiteController extends AbstractController
{
    #[Route('/private-liste-identite', name: 'liste-identite')]
    public function listeIdentite(): Response
    {
        $repoIdentite = $this->getDoctrine()->getRepository(Identite::class);
        $identite = $repoIdentite->findAll();
        return $this->render('identite/liste-identite.html.twig', [
           'identite' => $identite,
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

        #[Route('/private-accepter-demande', name: 'accepter-demande', requirements: ["email"=>"\d+"] )] 
        public function accepterDemande(MailerInterface $mailer, String $email): Response {
            $mail = (new TemplatedEmail())
            ->from('loupmayeur2003@gmail.com')
            ->to($email)
            ->subject("Votre dossier a été accepté")
            ->htmlTemplate('emails/emailDossier.html.twig')
            ->context([

                ]);
            $mailer->send($mail);
            $this->addFlash('notice','Notification envoyé, demande prise en charge');
            return $this -> redirectToRoute('liste-identite');
        }


        #[Route('/private-carte-recu/{id}', name: 'carte-recu', requirements: ["id"=>"\d+", "email"=>"\d+"] )] 
        public function carteRecu(MailerInterface $mailer, int $id, String $email): Response {
            $mail = (new TemplatedEmail())
            ->from('loupmayeur2003@gmail.com')            
            ->to($this->getUser()->getEmail())
            ->subject("Votre nouvelle carte d'identitée est disponible")
            ->htmlTemplate('emails/emailCarte.html.twig')
            ->context([

                ]);
            $mailer->send($mail);
            $demande = $this->getDoctrine()->getRepository(Identite::class)->find($id); 
            $em = $this->getDoctrine()->getManager();
            $em->remove($demande);
            $em->flush();
            $this->addFlash('notice','Notification envoyé, carte dispo');
            return $this -> redirectToRoute('liste-identite');
        }

        #[Route('/private-supprimer-demande/{id}', name: 'supprimer-demande', requirements: ["id"=>"\d+", "email"=>"\d+", "idDomicile"=>"\d+", "idCarte"=>"\d+"] )] 
        public function supprimerDemande(MailerInterface $mailer, int $id, String $email, int $idDomicile, int $idCarte): Response {
            $mail = (new TemplatedEmail())
            ->from('loupmayeur2003@gmail.com')            
            ->to($email)
            ->subject("Votre demande de nouvelle carte d'identité à été rejeté")
            ->htmlTemplate('emails/emailRefuse.html.twig')
            ->context([

                ]);
            $mailer->send($mail);
            $repoFichier = $doctrine->getRepository(Fichier::class);         
            $request = $repoFichier->find($idDomicile);
            $request2 = $repoFichier->find($idCarte);
            if($request != null && $request2 != null){
                $f = $doctrine->getRepository(Fichier::class)->find($request); 
                $ff = $doctrine->getRepository(Fichier::class)->find($request2);
                try {
                $filesystem = new Filesystem();
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$f->getNomServeur())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$f->getNomServeur());
                }
                if ($filesystem->exists($this->getParameter('file_directory').'/'.$ff->getNomServeur())){
                    $filesystem->remove($this->getParameter('file_directory').'/'.$ff->getNomServeur());
                }
                } catch (IOExceptionInterface $exception) {
                $this->addFlash('notice', 'Erreur');
                }
            }
            $demande = $this->getDoctrine()->getRepository(Identite::class)->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($demande);
            $em->flush();
            $em->remove($f);
            $em->remove($ff);
            $em->flush();
            $this->addFlash('notice','Notification envoyé, demande rejeté');
            return $this -> redirectToRoute('liste-identite');
        }
}
