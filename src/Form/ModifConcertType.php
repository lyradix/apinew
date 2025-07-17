<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Scene;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
              ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'mapped' => false,
                'data' => $options['data']->getStartTime() ?? null,
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
           ->add('image', FileType::class, [
                'label' => 'Ajouter image',
                'required' => false,
                'mapped' => false, //set to false if image is not directly mapped to entity
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webb'],
                        'mimeTypesMessage' => 'Merci de télécharger une image'
                    ])   
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
