<?php

namespace App\Form;

use App\Entity\Artist;
use App\Entity\Scene;
use App\Dto\ConcertDto;
use App\Form\ArtistType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('artist', ArtistType::class, [
                'label' => false,
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'mapped' => false,
                'data' => $options['data']->artist ? $options['data']->artist->getStartTime() : null,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Ajouter image',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de télécharger une image valide (JPEG, PNG, WEBP)',
                    ])
                ]
            ])
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
            'data_class' => ConcertDto::class,
        ]);
    }
}
