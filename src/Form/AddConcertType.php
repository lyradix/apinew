<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Scene;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
           ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
             ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début'
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin'
            ])
            ->add('famousSong')
            ->add('genre')
            ->add('description')
            ->add('source')
            ->add('lien')
            ->add('sceneFK', EntityType::class, [
                'class' => Scene::class,
                'choice_label' => 'nom',
                'label' => 'Scène',
                'placeholder' => 'Sélectionnez une scène',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
