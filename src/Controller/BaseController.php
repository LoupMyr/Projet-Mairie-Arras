<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Contact;
use App\Form\AvisType;
use App\Entity\Avis;
use App\Form\CantineType;
use App\Entity\Cantine;
use App\Form\IdentiteType;
use App\Entity\Identite;

        
class BaseController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        $repoAvis = $this->getDoctrine()->getRepository(avis::class);
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

    #[Route('/private-avis', name: 'avis')]
    public function avis(Request $request): Response
    {
        $avis = new avis();
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

    #[Route('/private-cantine', name: 'cantine')]
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


    #[Route('/private-identite', name: 'identite')]
    public function identite(Request $request): Response
    {
        $identite = new Identite();
        $form = $this->createForm(IdentiteType::class, $identite);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isSubmitted()&&$form->isValid()){
                $identite->getNom();
                $identite->getPrenom();
                $identite->getDateNaissance();
                $identite->getLieuNaissance();
                $identite->getAdresse();
                $identite->getCodePostal();

            $em = $this->getDoctrine()->getManager();
            $em->persist($identite);
            $em->flush();

            $this->addFlash('notice','Demande effectué');
            return $this->redirectToRoute('identite');
         }
    }

    return $this->render('base/identite.html.twig', [
        'form' => $form->createView()
    ]);
}    
}