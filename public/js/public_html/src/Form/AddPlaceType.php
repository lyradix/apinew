<?php

namespace App\Form;

use App\Entity\Poi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                 ->add('nom', TextType::class, [
                'label' => 'Nom du lieu',
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'scale' => 8,
                  'attr' => [
                'placeholder' => 'exemple : 2.3522',
            ],
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'scale' => 8,
                  'attr' => [
                'placeholder' => 'exemple : 48.8566',
            ],
            ])
            ->add('type', ChoiceType::class, [
    'label' => 'Type',
    'choices' => $options['type_choices'] ?? [],
    'mapped' => false, 
])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poi::class,
            'type_choices' => [], 
        ]);
    }
}

