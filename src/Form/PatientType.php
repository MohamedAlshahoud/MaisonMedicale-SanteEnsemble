<?php

namespace App\Form;

use App\Entity\Patient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PatientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom'
        ])
        ->add('prenom', TextType::class, [
            'label' => 'Prénom'
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email'
        ])
        ->add('plainMotDePasse', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false, // Ce champ ne doit pas être directement mappé à l'entité
            'required' => false,
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Confirmez le mot de passe'],
        ])
        ->add('dateNaissance', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de naissance'
        ])
        ->add('adresse', TextType::class, [
            'label' => 'Adresse'
        ])
        ->add('telephone', TelType::class, [
            'label' => 'Téléphone'
        ])
        ->add('submit', SubmitType::class, [
            'label' => $options['submit_label'] ?? 'Valider'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
            'submit_label' => 'S\'inscrire' // valeur par défaut
        ]);
    }
}
