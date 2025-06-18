<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\ChangePasswordFormType;
use App\Form\PatientEditType;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class PatientController extends AbstractController
{
    #[Route('/patient/{id}', name: 'patient_profile')]
    public function profile(Patient $patient): Response
    {
        return $this->render('patient/index.html.twig', [
            'patient' => $patient,
        ]);
    }

    #[Route('/patient/{id}/rendez-vous', name: 'patient_rendezvous')]
    public function rendezVous(Patient $patient): Response
    {
        return $this->render('patient/rendezvous.html.twig', [
            'patient' => $patient,
            'rendezvous' => $patient->getRendezVous(),
        ]);
    }


    #[Route('/patient/{id}/edit', name: 'patient_edit', requirements: ['id' => Requirement::DIGITS])]
    public function edit(Request $request, Patient $patient, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PatientEditType::class, $patient);
        $form->handleRequest($request);

        // Supprime l'obligation de changer le mot de passe
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('patient_profile', ['id' => $patient->getId()]);
        }

        return $this->render('patient/edit.html.twig', [
            'form' => $form->createView(),
            'patient' => $patient,
        ]);
    }

    
    
    #[Route('/patient/{id}/change-password', name: 'patient_change_password')]
    public function changePassword(
        Patient $patient,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier que l'utilisateur connecté est bien le patient concerné (sécurité)
        if ($this->getUser()->getId() !== $patient->getId()) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Vérifier que le mot de passe actuel est correct
            if (!$passwordHasher->isPasswordValid($patient, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            } else {
                // Encoder le nouveau mot de passe
                $hashedPassword = $passwordHasher->hashPassword($patient, $newPassword);

                // Mettre à jour le mot de passe dans l'entité Patient
                $patient->setPassword($hashedPassword);

                $entityManager->persist($patient);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour.');

                // Rediriger par exemple vers le profil
                return $this->redirectToRoute('patient_profile', ['id' => $patient->getId()]);
            }
        }

        return $this->render('patient/changepassword.html.twig', [
            'form' => $form->createView(),
            'patient' => $patient,
        ]);
    }

}
