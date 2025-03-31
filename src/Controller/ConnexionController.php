<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('admin_dashboard'); // 🔥 Redirige un admin vers le dashboard
            } else {
                return $this->redirectToRoute('patient_profile', ['id' => $user->getId()]); // 🔥 Redirige un patient vers son profil
            }
        }

        // Récupérer les erreurs de connexion si elles existent
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connexion/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion, ce code ne sera jamais exécuté.
    }
}
