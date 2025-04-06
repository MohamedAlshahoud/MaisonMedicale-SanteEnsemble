<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\RendezVous;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RendezVousController extends AbstractController
{
    #[Route('/rendez-vous', name: 'rendez_vous_step_1')]
    public function step1(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les médecins généralistes depuis la base de données
        $medecins = $entityManager->getRepository(Medecin::class)->findBy(['isGeneraliste' => true]);

        // Vérifier s'il y a des médecins généralistes disponibles
        if (empty($medecins)) {
            $this->addFlash('error', 'Aucun médecin disponible');
            return $this->redirectToRoute('app_accueil'); // Rediriger vers la page d'accueil
        }

        // Création du formulaire pour choisir un médecin
        $form = $this->createFormBuilder()
            ->add('medecin', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(function ($medecin) {
                        return $medecin->getNom() . ' ' . $medecin->getPrenom(); // Affichage du nom complet du médecin
                    }, $medecins),
                    $medecins // Associer chaque médecin à l'objet Medecin
                ),
                'label' => 'Choisissez un médecin'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Continuer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medecinChoisi = $form->get('medecin')->getData();
            // Stocker le médecin choisi dans la session
            $request->getSession()->set('medecin', $medecinChoisi);

            // Rediriger vers la page suivante (choisir une disponibilité)
            return $this->redirectToRoute('rendez_vous_step_2');
        }

        return $this->render('rendez_vous/reserve_medecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendez-vous/choisir-disponibilite', name: 'rendez_vous_step_2')]
    public function step2(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le médecin choisi dans la session
        $medecinChoisi = $request->getSession()->get('medecin');

        if (!$medecinChoisi) {
            return $this->redirectToRoute('rendez_vous_step_1'); // Si le médecin n'est pas défini, revenir à la première étape
        }

        // Pour l'exemple, chaque médecin a des créneaux de disponibilité
        $disponibilites = ['2025-03-30 10:00', '2025-03-30 14:00', '2025-03-31 09:00']; // Ceci devrait venir de la base de données

        // Création du formulaire pour choisir une disponibilité
        $form = $this->createFormBuilder()
            ->add('disponibilite', ChoiceType::class, [
                'choices' => array_combine($disponibilites, $disponibilites),
                'label' => 'Choisissez une disponibilité'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Confirmer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disponibiliteChoisie = $form->get('disponibilite')->getData();

            // Créer un rendez-vous avec le médecin choisi et la disponibilité sélectionnée
            $rendezVous = new RendezVous();
            $rendezVous->setMedecin($medecinChoisi);
            $rendezVous->setDateHeure(new \DateTime($disponibiliteChoisie));

            // Sauvegarder le rendez-vous dans la base de données
            $entityManager->persist($rendezVous);
            $entityManager->flush();

            // Afficher un message de succès ou rediriger vers une autre page
            return $this->render('rendez_vous/success.html.twig', [
                'rendezVous' => $rendezVous,
            ]);
        }

        return $this->render('rendez_vous/disponibilte.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

