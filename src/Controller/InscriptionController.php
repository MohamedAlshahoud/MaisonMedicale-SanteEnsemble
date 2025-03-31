<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, EntityManagerInterface $entityManager, PatientMotDePasseEncoderInterface $passwordEncoder): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encoder le mot de passe
            $hashedPassword = $passwordEncoder->encodePassword($patient, $form->get('password')->getData());
            $patient->setMotDePasse($hashedPassword);

            // Définir le rôle par défaut
            $patient->setRoles(['ROLE_PATIENT']);

            $entityManager->persist($patient);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('inscription/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
