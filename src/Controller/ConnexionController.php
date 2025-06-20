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
        // Si l'utilisateur est déjà connecté, on redirige selon son rôle
        $user = $this->getUser();

        if ($user !== null) {
            $roles = is_array($user->getRoles()) ? $user->getRoles() : [];

            if (in_array('ROLE_ADMIN', $roles, true)) {
                return $this->redirectToRoute('admin_dashboard');
            }

            return $this->redirectToRoute('patient_profile', ['id' => $user->getId()]);
        }

        // Affiche la page de connexion avec les éventuelles erreurs
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connexion/connexion.html.twig', [
            'last_username' => $lastUsername ?? '',
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_deconnexion')]
    public function logout(): void
    {
        // Cette méthode peut rester vide : Symfony s'occupe de la déconnexion automatiquement
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
