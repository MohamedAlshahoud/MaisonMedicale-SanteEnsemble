<?php

namespace App\Form;

use App\Entity\Disponibilite;
use App\Entity\Medecin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('medecin', EntityType::class, [
                'class' => Medecin::class,
                'choice_label' => fn($m) => $m->getNom().' '.$m->getPrenom(),
                'label' => 'Médecin',
            ])
            ->add('debut', DateTimeType::class, [
                'label' => 'Date/heure de début',
                'widget' => 'single_text',
            ])
            ->add('fin', DateTimeType::class, [
                'label' => 'Date/heure de fin',
                'widget' => 'single_text',
            ])
            ->add('estLibre', CheckboxType::class, [
                'label' => 'Disponible ?',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Disponibilite::class,
        ]);
    }
}
