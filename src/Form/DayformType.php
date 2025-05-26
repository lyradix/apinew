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
                'label' => 'vendredi', 
                'required' => false, 
                'mapped' => false, 
            ]);
            $builder
            ->add('samedi', CheckboxType::class, [
                'label' => 'Samedi', 
                'required' => false, 
                'mapped' => false, 
            ]);
            $builder
            ->add('dimanche', CheckboxType::class, [
                'label' => 'dimanche', 
                'required' => false, 
                'mapped' => false, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Days::class,
        ]);
    }
}
