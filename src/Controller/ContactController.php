<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, ContactRepository $contactRepository): Response
    {

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->save($contact, true);

            // envoi email

            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
            $message = ('Votre message a été envoyé, un conseiller vous répondra très rapidement');
            $this->addFlash('contact_success', $message);
        }
        if($form->isSubmitted() && !$form->isValid()){
            $error = ('Le formulaire contient des erreurs. Veuillez corriger et réessayer .');
            $this->addFlash('contact_error', $error);
        }

        return $this->render('contact/index.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
