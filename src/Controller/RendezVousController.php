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

        if (empty($medecins)) {
            $this->addFlash('error', 'Aucun médecin disponible');
            return $this->redirectToRoute('app_accueil');
        }

        // Création du formulaire pour choisir un médecin
        $form = $this->createFormBuilder()
            ->add('medecin', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($medecin) => $medecin->getNom() . ' ' . $medecin->getPrenom(), $medecins),
                    array_map(fn($medecin) => $medecin->getId(), $medecins)
                ),
                'label' => 'Choisissez un médecin'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Continuer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medecinId = $form->get('medecin')->getData();

            // Stocker seulement l'ID du médecin en session
            $request->getSession()->set('medecin_id', $medecinId);

            return $this->redirectToRoute('rendez_vous_step_2');
        }

        return $this->render('rendez_vous/reserve_medecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendez-vous/choisir-disponibilite', name: 'rendez_vous_step_2')]
    public function step2(Request $request, EntityManagerInterface $entityManager): Response
    {
        $medecinId = $request->getSession()->get('medecin_id');
        if (!$medecinId) {
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        // Récupérer le médecin depuis la base via l'ID
        $medecinChoisi = $entityManager->getRepository(Medecin::class)->find($medecinId);
        if (!$medecinChoisi) {
            $this->addFlash('error', 'Médecin non trouvé.');
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        $disponibilites = ['2025-03-30 10:00', '2025-03-30 14:00', '2025-03-31 09:00'];

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

            $rendezVous = new RendezVous();
            $rendezVous->setMedecin($medecinChoisi);
            $rendezVous->setDateHeure(new \DateTime($disponibiliteChoisie));

            $patient = $this->getUser();
            if (!$patient) {
                $this->addFlash('error', 'Vous devez être connecté pour prendre un rendez-vous.');
                return $this->redirectToRoute('app_login');
            }
            $rendezVous->setPatient($patient);

            $entityManager->persist($rendezVous);
            $entityManager->flush();

            return $this->render('rendez_vous/success.html.twig', [
                'rendezVous' => $rendezVous,
            ]);
        }

        return $this->render('rendez_vous/disponibilte.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
