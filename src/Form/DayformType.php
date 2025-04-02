<?php

namespace App\Form;

use App\Entity\Days;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vendredi', CheckboxType::class, [
                'label' => 'vendredi', // Label for the checkbox
                'required' => false, // Checkbox is optional
                'mapped' => false, // Not directly mapped to the Days entity
            ]);
            $builder
            ->add('samedi', CheckboxType::class, [
                'label' => 'Samedi', // Label for the checkbox
                'required' => false, // Checkbox is optional
                'mapped' => false, // Not directly mapped to the Days entity
            ]);
            $builder
            ->add('dimanche', CheckboxType::class, [
                'label' => 'dimanche', // Label for the checkbox
                'required' => false, // Checkbox is optional
                'mapped' => false, // Not directly mapped to the Days entity
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Days::class,
        ]);
    }
}
