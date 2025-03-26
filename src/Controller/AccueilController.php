<?php

namespace App\Controller;

use App\Entity\Specialite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{

    private $entityManager;

    // Injection de l'EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        // Récupérer toutes les spécialités depuis la base de données
        $specialites = $this->entityManager->getRepository(Specialite::class)->findAll();

        // Renvoyer la vue avec les spécialités
        return $this->render('accueil/index.html.twig', [
            'specialites' => $specialites,
        ]);
    }

    // Nouvelle route pour afficher toutes les spécialités
    #[Route('/specialites', name: 'toutes_specialites')]
    public function toutesSpecialites(): Response
    {
        // Récupérer toutes les spécialités depuis la base de données
        $specialites = $this->entityManager->getRepository(Specialite::class)->findAll();

        // Renvoyer la vue avec toutes les spécialités
        return $this->render('accueil/toutes_specialites.html.twig', [
            'specialites' => $specialites,
        ]);
    }
}
