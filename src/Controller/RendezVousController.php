<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Entity\RendezVous;
use App\Repository\DisponibiliteRepository;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RendezVousController extends AbstractController
{
    #[Route('/rendez-vous', name: 'rendez_vous_step_1')]
    public function step1(Request $request, EntityManagerInterface $entityManager): Response
    {
        $medecins = $entityManager->getRepository(Medecin::class)->findBy(['isGeneraliste' => true]);

        if (empty($medecins)) {
            $this->addFlash('error', 'Aucun médecin disponible');
            return $this->redirectToRoute('app_accueil');
        }

        $form = $this->createFormBuilder()
            ->add('medecin', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($m) => $m->getNom() . ' ' . $m->getPrenom(), $medecins),
                    array_map(fn($m) => $m->getId(), $medecins)
                ),
                'label' => 'Choisissez un médecin'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Continuer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medecinId = $form->get('medecin')->getData();
            $request->getSession()->set('medecin_id', $medecinId);
            return $this->redirectToRoute('rendez_vous_step_2');
        }

        return $this->render('rendez_vous/reserve_medecin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/rendez-vous/choisir-disponibilite', name: 'rendez_vous_step_2')]
    public function step2(Request $request, MedecinRepository $medecinRepo, DisponibiliteRepository $dispoRepo): Response
    {
        $medecinId = $request->getSession()->get('medecin_id');
        if (!$medecinId) {
            $this->addFlash('error', 'Médecin non sélectionné.');
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        $medecin = $medecinRepo->find($medecinId);
        if (!$medecin) {
            $this->addFlash('error', 'Médecin introuvable.');
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        $disponibilites = $dispoRepo->findDisponibilitesByMedecin($medecin);

        $form = $this->createFormBuilder()
            ->add('disponibilite', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($d) => $d->getDebut()->format('d/m/Y H:i'), $disponibilites),
                    array_map(fn($d) => $d->getId(), $disponibilites)
                ),
                'label' => 'Choisissez une disponibilité',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Continuer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $disponibiliteId = $form->get('disponibilite')->getData();
            $request->getSession()->set('disponibilite_id', $disponibiliteId);
            return $this->redirectToRoute('rendez_vous_step_3');
        }

        return $this->render('rendez_vous/disponibilite.html.twig', [
            'form' => $form->createView(),
            'medecin' => $medecin,
        ]);
    }

    #[Route('/rendez-vous/confirmation', name: 'rendez_vous_step_3')]
    public function step3(
        Request $request,
        EntityManagerInterface $em,
        DisponibiliteRepository $dispoRepo,
        MedecinRepository $medecinRepo
    ): Response {
        $medecinId = $request->getSession()->get('medecin_id');
        $disponibiliteId = $request->getSession()->get('disponibilite_id');

        if (!$medecinId || !$disponibiliteId) {
            $this->addFlash('error', 'Données manquantes pour la confirmation.');
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        $medecin = $medecinRepo->find($medecinId);
        $disponibilite = $dispoRepo->find($disponibiliteId);

        if (!$disponibilite) {
            $this->addFlash('error', 'Cette disponibilité n’est plus valide ou a déjà été réservée.');
            return $this->redirectToRoute('rendez_vous_step_1');
        }

        $user = $this->getUser();
        if (!$user instanceof \App\Entity\Patient) {
            throw new \LogicException('L’utilisateur connecté n’est pas un patient.');
        }

        $form = $this->createFormBuilder()
            ->add('confirm', SubmitType::class, ['label' => 'Confirmer le rendez-vous'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rendezVous = new RendezVous();
            $rendezVous->setMedecin($medecin);
            $rendezVous->setDateHeure($disponibilite->getDebut());
            $rendezVous->setPatient($user);

            // Supprime la disponibilité après réservation
            $em->remove($disponibilite);
            $em->persist($rendezVous);
            $em->flush();

            $request->getSession()->remove('medecin_id');
            $request->getSession()->remove('disponibilite_id');

            return $this->redirectToRoute('rendez_vous_success', ['id' => $rendezVous->getId()]);
        }

        return $this->render('rendez_vous/confirmation.html.twig', [
            'form' => $form->createView(),
            'medecin' => $medecin,
            'disponibilite' => $disponibilite,
        ]);
    }

    #[Route('/rendez-vous/success/{id}', name: 'rendez_vous_success')]
    public function success(RendezVous $rendezVous): Response
    {
        return $this->render('rendez_vous/success.html.twig', [
            'rendezVous' => $rendezVous,
        ]);
    }

    #[Route('/mes-rendezvous', name: 'mes_rendezvous')]
    public function mesRendezvous(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\Patient) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $rendezvous = $em->getRepository(RendezVous::class)->findBy(['patient' => $user], ['dateHeure' => 'ASC']);

        return $this->render('patient/rendezvous.html.twig', [
            'rendezvous' => $rendezvous,
            'patient' => $user,
        ]);
    }

    #[Route('/rendez-vous/annuler/{id}', name: 'rendez_vous_annuler', methods: ['GET', 'POST'])]
    public function annuler(RendezVous $rendezVous, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\Patient || $rendezVous->getPatient()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler ce rendez-vous.');
        }

        // Recréer la disponibilité associée au rendez-vous
        $disponibilite = new \App\Entity\Disponibilite();
        $disponibilite->setMedecin($rendezVous->getMedecin());
        $disponibilite->setDebut($rendezVous->getDateHeure());
        $disponibilite->setFin((clone $rendezVous->getDateHeure())->modify('+30 minutes')); // ou la durée exacte
        $disponibilite->setEstLibre(true);

        $em->persist($disponibilite);
        $em->remove($rendezVous);
        $em->flush();

        $this->addFlash('success', 'Rendez-vous annulé avec succès.');

        return $this->redirectToRoute('mes_rendezvous');
    }
}
