<?php

namespace App\Form;

use App\Entity\Poi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeletePoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // No fields needed, just CSRF
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
