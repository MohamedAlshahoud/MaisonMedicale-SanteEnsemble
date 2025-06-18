<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($contact);
                $entityManager->flush();

                $message = 'Votre message a été envoyé, un conseiller vous répondra très rapidement';
                $this->addFlash('contact_success', $message);

                // Reset form
                $contact = new Contact();
                $form = $this->createForm(ContactType::class, $contact);
            } else {
                $error = 'Le formulaire contient des erreurs. Veuillez corriger et réessayer.';
                $this->addFlash('contact_error', $error);
            }
        }

        if ($request->isXmlHttpRequest()) {
            // Récupérer les messages flash
            $successMessages = $this->get('session')->getFlashBag()->get('contact_success', []);
            $errorMessages = $this->get('session')->getFlashBag()->get('contact_error', []);

            $formView = $this->renderView('contact/_form.html.twig', [
                'form' => $form->createView(),
            ]);

            return new JsonResponse([
                'form' => $formView,
                'successMessages' => $successMessages,
                'errorMessages' => $errorMessages,
            ]);
        }

        return $this->render('contact/index.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
