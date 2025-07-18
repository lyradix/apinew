<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Scene;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de début'
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de fin'
            ])
            ->add('famousSong', TextType::class, [
                'label' => 'Chanson célèbre',
                'required' => false
            ])
            ->add('genre', TextType::class, [
                'label' => 'Genre',
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false
            ])
            ->add('source', TextType::class, [
                'label' => 'Source',
                'required' => false
            ])
            ->add('lien', TextType::class, [
                'label' => 'Lien',
                'required' => false
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
