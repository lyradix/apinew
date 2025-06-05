<?php

namespace App\Form;

use App\Entity\Poi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewSceneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         ->add('nom', TextType::class, [
                'label' => 'Nom de la scène'
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'scale' => 8,
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'scale' => 8,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Poi::class,
        ]);
    }
}
