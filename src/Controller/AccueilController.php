<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AccueilController extends AbstractController
{
    private $medecinRepository;

    
    public function __construct(MedecinRepository $medecinRepository)
    {
        $this->medecinRepository = $medecinRepository;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        // Récupérer tous les médecins généralistes depuis la base de données
        // Ajouter un filtre si nécessaire, par exemple en vérifiant si les médecins sont généralistes


        // Renvoyer la vue avec les médecins
        return $this->render('accueil/index.html.twig', [
            'medecins' => $this->medecinRepository->findAll()
        ]);
    }

}