<?php

namespace App\Controller;

use App\Entity\Medecin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        // Récupérer tous les médecins généralistes depuis la base de données
        // Ajouter un filtre si nécessaire, par exemple en vérifiant si les médecins sont généralistes
        $medecins = $this->entityManager->getRepository(Medecin::class)->findBy(['isGeneraliste' => true]);

        // Renvoyer la vue avec les médecins
        return $this->render('accueil/index.html.twig', [
            'medecins' => $medecins,
        ]);
    }

    // Nouvelle route pour afficher tous les médecins généralistes
    #[Route('/medecins', name: 'tous_medecins')]
    public function tousMedecins(): Response
    {
        // Récupérer tous les médecins généralistes depuis la base de données
        // Si tu veux un affichage spécifique des médecins généralistes
        //$medecins = $this->entityManager->getRepository(Medecin::class)->findBy(['isGeneraliste' => true]);
        $medecins = $this->entityManager->getRepository(Medecin::class)->findAll();

        // Renvoyer la vue avec tous les médecins
        return $this->render('accueil/tous_medecins.html.twig', [
            'medecins' => $medecins,
        ]);
    }
}