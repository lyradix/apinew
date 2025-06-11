<?php

namespace App\Form;

use App\Entity\Info;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UpdateInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', ChoiceType::class, [
                'choices' => $options['title_choices'],
                'label' => 'Titre',
                'placeholder' => 'Choisir un titre',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $options['type_choices'],
                'label' => 'Type', // Correct label
                'placeholder' => 'Choisir un type',
            ])
            ->add('descriptif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Info::class,
            'title_choices' => [],
            'type_choices' => [],
        ]);
    }
}
