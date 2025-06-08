<?php

namespace App\Form;

use App\Entity\Partners;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPartnersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
            'label' => 'Titre'
        ])
            ->add('frontPage', null, [
            'label' => 'Afficher sur page d\'accueil'
        ])
            ->add('type', ChoiceType::class, [
            'label' => 'Type',
            'choices' => [
                'Restaurent' => 'Restaurent',
                'Sponsor' => 'Sponsor',
                'Media' => 'Media',
            ],
            'placeholder' => 'Choisir un type',
        ])
            ->add('link', null, [
            'label' => 'Lien'
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partners::class,
        ]);
    }
}
