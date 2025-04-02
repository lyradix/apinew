<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Days;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jour')
            ->add('userFK', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('artistFK', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('jour', ChoiceType::class, [
                'label' => 'Select Days',
                'choices' => [
                    'Monday' => 'monday',
                    'Tuesday' => 'tuesday',
                    'Wednesday' => 'wednesday',
                    'Thursday' => 'thursday',
                    'Friday' => 'friday',
                    'Saturday' => 'saturday',
                    'Sunday' => 'sunday',
                ],
                'multiple' => true, // Allow multiple selections
                'expanded' => true, // Render as checkboxes
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Days',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Days::class,
        ]);
    }
}
