<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

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
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        // Supprime l'obligation de changer le mot de passe
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainMotDePasse')->getData();

            if (!empty($plainPassword)) {
                // Assure-toi d'avoir un service pour encoder le mot de passe
                $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
                $patient->setMotDePasse($hashedPassword);
            }

            $em->flush();

            $this->addFlash('success', 'Profil mis à jour.');
            return $this->redirectToRoute('patient_profile', ['id' => $patient->getId()]);
        }

        return $this->render('patient/edit.html.twig', [
            'form' => $form->createView(),
            'patient' => $patient,
        ]);
    }


}
