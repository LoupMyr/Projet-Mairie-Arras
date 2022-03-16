<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;

 
class ContactController extends AbstractController
{
    #[Route('/private-liste-contacts', name: 'liste-contacts')]
    public function listeContacts(): Response
    {
        $repoContact = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $repoContact->findAll();
        return $this->render('contact/liste-contacts.html.twig', [
           'contacts' => $contacts
        ]);
    }
}