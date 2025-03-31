<?php

namespace App\Controller;

use App\Entity\Patient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PatientController extends AbstractController
{
    #[Route('/patient/{id}', name: 'patient_profile')]
    public function profile(Patient $patient): Response
    {
        return $this->render('patient/index.html.twig', [
            'patient' => $patient
        ]);
    }
}
