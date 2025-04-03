<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\PatientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la valeur du champ de mot de passe
            $plainMotDePasse = $form->get('plainMotDePasse')->getData();
        
            if ($plainMotDePasse) {
                // Encoder le mot de passe
                $hashedPassword = $passwordHasher->hashPassword($patient, $plainMotDePasse);
                $patient->setMotDePasse($hashedPassword);
            }
        
            // Définir le rôle par défaut
            $patient->setRoles(['ROLE_PATIENT']);
        
            $entityManager->persist($patient);
            $entityManager->flush();
        
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
        
            return $this->redirectToRoute('app_connexion');
        }
        

        return $this->render('inscription/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
}
