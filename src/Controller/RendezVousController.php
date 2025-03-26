<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\Specialite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RendezVousController extends AbstractController
{
    #[Route('/rendez-vous', name: 'rendez_vous')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        // Récupérer les spécialités et les médecins
        $specialites = $entityManager->getRepository(Specialite::class)->findAll();
        $medecins = $entityManager->getRepository(Medecin::class)->findAll();

        // Passer les données à la vue
        return $this->render('rendez_vous/index.html.twig', [
            'specialites' => $specialites,
            'medecins' => $medecins,
        ]);
    }
}
