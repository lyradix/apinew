<?php

namespace App\Form;

use App\Entity\Poi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifPlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poi_id', ChoiceType::class, [
                'choices' => $options['poi_choices'],
                'label' => 'Lieu à modifier',
                'required' => true,
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
]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, 
            'type_choices' => [],
            'poi_choices' => [],
        ]);
    }
}
