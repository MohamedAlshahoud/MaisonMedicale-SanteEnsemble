<?php

namespace App\Form;

use App\Entity\Disponibilite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ChoixDisponibiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('disponibilite', EntityType::class, [
                'class' => Disponibilite::class,
                'choice_label' => function (Disponibilite $dispo) {
                    // Format affichage date début - date fin + médecin
                    $debut = $dispo->getDebut()->format('d/m/Y H:i');
                    $fin = $dispo->getFin()->format('H:i');
                    $medecinNom = $dispo->getMedecin() ? $dispo->getMedecin()->getNom() . ' ' . $dispo->getMedecin()->getPrenom() : 'Médecin inconnu';

                    return sprintf('%s - %s (%s)', $debut, $fin, $medecinNom);
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->where('d.estLibre = :libre')
                        ->setParameter('libre', true)
                        ->orderBy('d.debut', 'ASC');
                },
                'label' => 'Choisissez une disponibilité',
                'placeholder' => 'Sélectionnez une disponibilité',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas lié à une entité, formulaire simple
            'data_class' => null,
        ]);
    }
}
