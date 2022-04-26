<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Contact;
use App\Form\AvisType;
use App\Entity\Avis;
use App\Form\CantineType;
use App\Entity\Cantine;
use App\Form\IdentiteType;
use App\Entity\Identite;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Fichier;
        
class BaseController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        $repoAvis = $this->getDoctrine()->getRepository(Avis::class);
        $avis = $repoAvis->findAll();
      
        return $this->render('base/index.html.twig', [
            'avis' => $avis
        ]);
    }

#[Route('/contact', name: 'contact')]
public function contact(Request $request, MailerInterface $mailer): Response
{
    $contact = new Contact();
    $form = $this->createForm(ContactType::class, $contact);

    if($request->isMethod('POST')){
        $form->handleRequest($request);
        if ($form->isSubmitted()&&$form->isValid()){   
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('loupmayeur2003@gmail.com')
            ->subject($contact->getSujet())
            ->htmlTemplate('emails/email.html.twig')
            ->context([
                'nom'=> $contact->getNom(),
                'sujet'=> $contact->getSujet(),
                'message'=> $contact->getMessage()
            ]);
            $contact->setDateEnvoi(new \Datetime());  
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
          
            $mailer->send($email);
            $this->addFlash('notice','Message envoyé');
            return $this->redirectToRoute('contact');
        }
    }

    return $this->render('base/contact.html.twig', [
        'form' => $form->createView()
    ]);
    }

    #[Route('/profile-avis', name: 'avis')]
    public function avis(Request $request): Response
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                    $avis->getNom();
                    $avis->getPrenom();
                    $avis->getNote();
                    $avis->getMessage();
                    $avis->setDateEnvoi(new \Datetime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($avis);
            $em->flush();

            $this->addFlash('notice','Merci pour votre avis!');
            return $this->redirectToRoute('index');
            }
        }
        return $this->render('base/avis.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/monuments', name: 'monuments')]
    public function monuments(): Response
    {
        return $this->render('base/monuments.html.twig', [
                
        ]);
    }

    #[Route('/profile-cantine', name: 'cantine')]
    public function cantine(Request $request): Response
    {
        $cantine = new cantine();
        $form = $this->createForm(CantineType::class, $cantine);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                    $cantine->getNom();
                    $cantine->getPrenom();
                    $cantine->getDate();
                    $cantine->getEcole();

            $em = $this->getDoctrine()->getManager();
            $em->persist($cantine);
            $em->flush();
                $this->addFlash('notice','Réservation effectué');
                return $this->redirectToRoute('cantine');
            }
        }
        return $this->render('base/cantine.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/profile-identite', name: 'identite')]
    public function identite(Request $request, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
        $identite = new Identite();
        $form = $this->createForm(IdentiteType::class, $identite);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            $domicile = $form->get('domicile')->getData();
            $carte = $form->get('carte')->getData();
            if ($form->isSubmitted()&&$form->isValid()){
                if($domicile && $carte){      
                    $domicile = $form->get('domicile')->getData();
                    $carte = $form->get('carte')->getData();                                                  
                    $nomDomicile = pathinfo($domicile->getClientOriginalName(),PATHINFO_FILENAME);                    
                    $nomDomicile = $slugger->slug($nomDomicile);                    
                    $nomDomicile = $nomDomicile.'-'.uniqid().'.'.$domicile->guessExtension();

                    $nomCarte = pathinfo($carte->getClientOriginalName(),PATHINFO_FILENAME);                    
                    $nomCarte = $slugger->slug($nomCarte);                    
                    $nomCarte = $nomCarte.'-'.uniqid().'.'.$carte->guessExtension();                     
                    try{                              
                        $f = new Fichier(); 
                        $f->setNomServeur($nomDomicile);                        
                        $f->setNomOriginal($domicile->getClientOriginalName());                        
                        $f->setDateEnvoi(new \Datetime());                        
                        $f->setExtension($domicile->guessExtension());                        
                        $f->setTaille($domicile->getSize());                        
                        $f->setProprietaire($this->getUser());                        
                        $domicile->move($this->getParameter('file_directory'), $nomDomicile);
                        
                        $ff = new Fichier(); 
                        $ff->setNomServeur($nomCarte);                        
                        $ff->setNomOriginal($carte->getClientOriginalName());                        
                        $ff->setDateEnvoi(new \Datetime());                        
                        $ff->setExtension($carte->guessExtension());                        
                        $ff->setTaille($carte->getSize());                        
                        $ff->setProprietaire($this->getUser());                        
                        $carte->move($this->getParameter('file_directory'), $nomCarte);
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($f);
                        $em->persist($ff);
                        $em->flush();
                        $identite -> getDomicile($f);
                        $identite -> getCarte($ff);
                    }                               
                    catch(FileException $e){                        
                            $this->addFlash('notice', 'Erreur d\'envoi');                    
                        } 
                $identite->getNom();
                $identite->getPrenom();
                $identite->getEmail();
                $identite->getDateNaissance();
                $identite->getLieuNaissance();
                $identite->getAdresse();
                $identite->getCodePostal();
                $email = (new TemplatedEmail())
                ->from($this->getUser()->getEmail())
                ->to('loupmayeur2003@gmail.com')
                ->subject("Nouveau dossier identifiant")
                ->htmlTemplate('emails/dossier.html.twig')
                ->context([
                    'nom'=> $identite->getNom(),
                    'id'=> $identite->getId(),
                ]); 
                $this->addFlash('notice', 'Fichiers envoyé'); 
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($identite);
            $em->flush();
            $mailer->send($email);
            $this->addFlash('notice','Demande effectué');
            return $this->redirectToRoute('identite');
        }
           

        return $this->render('base/identite.html.twig', [
        'form' => $form->createView()
    ]);
   
}
}