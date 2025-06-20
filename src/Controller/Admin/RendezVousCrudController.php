<?php

namespace App\Controller\Admin;

use App\Entity\RendezVous;
use App\Entity\Disponibilite;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class RendezVousCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RendezVous::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('medecin'),
            AssociationField::new('patient'),
            AssociationField::new('disponibilite')
                ->setFormTypeOption('query_builder', function (\App\Repository\DisponibiliteRepository $repo) {
                    return $repo->createQueryBuilder('d')
                        ->where('d.estLibre = true');
                })
                ->setFormTypeOption('choice_label', function ($disponibilite) {
                    $medecin = $disponibilite->getMedecin();
                    return $disponibilite->getDebut()->format('d/m/Y H:i') 
                        . ' - ' 
                        . $disponibilite->getFin()->format('H:i') 
                        . ' (' . $medecin->getPrenom() . ' ' . $medecin->getNom() . ')';
                }),
        ];
    }

    // 🔁 Quand un nouveau rendez-vous est créé
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) return;

        $dispo = $entityInstance->getDisponibilite();
        if ($dispo) {
            $dispo->setEstLibre(false);
            $entityManager->persist($dispo);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    // 🔁 Quand un rendez-vous est modifié
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) return;

        $dispo = $entityInstance->getDisponibilite();
        if ($dispo) {
            $dispo->setEstLibre(false);
            $entityManager->persist($dispo);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof RendezVous) return;

        // On rend la disponibilité à nouveau libre
        $dispo = $entityInstance->getDisponibilite();
        if ($dispo) {
            $dispo->setEstLibre(true);
            $entityManager->persist($dispo);
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

}
