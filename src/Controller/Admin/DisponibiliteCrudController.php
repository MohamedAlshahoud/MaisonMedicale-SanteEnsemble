<?php

namespace App\Controller\Admin;

use App\Entity\Disponibilite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class DisponibiliteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Disponibilite::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('medecin')->setLabel('Médecin'),
            DateTimeField::new('debut')->setLabel('Début'),
            DateTimeField::new('fin')->setLabel('Fin'),
            BooleanField::new('estLibre')->setLabel('Est libre'),
        ];
    }
}
