<?php

namespace App\Form;

use App\Entity\Cours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'text-transform: uppercase;' // Affiche en majuscules
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le titre doit contenir au moins 3 caractères.'
                    ])
                ]
            ])
            ->add('nbchapitre', IntegerType::class, [
                'label' => 'Nombre de Chapitres',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nombre de chapitres est obligatoire.']),
                    new Assert\Positive(['message' => 'Le nombre de chapitres doit être un entier positif.'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit contenir au moins 10 caractères.'
                    ])
                ]
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La date est obligatoire.'])
                ]
            ])
            ->add('pdf', FileType::class, [
                'label' => 'Fichier PDF',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le fichier PDF est obligatoire.']),
                    new Assert\File([
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Le fichier doit être un PDF valide.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
