<?php

namespace App\Form;

use App\Entity\Aide;
use App\Entity\FormP;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class AideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'constraints' => [
                    new NotBlank(['message' => 'Le sujet est obligatoire.']),
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Le sujet doit contenir au moins 5 caractères.',
                        'maxMessage' => 'Le sujet ne peut pas dépasser 100 caractères.',
                    ])
                ],
                'attr' => ['placeholder' => 'Entrez le sujet de l\'aide', 'class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire.']),
                    new Length([
                        'min' => 10,
                        'max' => 1000,
                        'minMessage' => 'La description doit contenir au moins 10 caractères.',
                        'maxMessage' => 'La description ne peut pas dépasser 1000 caractères.',
                    ])
                ],
                'attr' => ['placeholder' => 'Décrivez votre problème', 'class' => 'form-control']
            ])
            ->add('date_creation', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de création',
                'constraints' => [
                    new NotBlank(['message' => 'La date de création est requise.'])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('form', EntityType::class, [
                'class' => FormP::class,
                'choice_label' => 'sujet',
                'placeholder' => 'Sélectionnez un formulaire',
                'label' => 'Formulaire associé',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner un formulaire.'])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-success']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Aide::class,
        ]);
    }
}
