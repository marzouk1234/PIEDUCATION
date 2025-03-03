<?php

namespace App\Form;

use App\Entity\Resultat;
use App\Entity\Evaluation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResultatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'attr' => ['class' => 'form-control', 'min' => 0, 'max' => 20],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La note ne peut pas être vide.']),
                    new Assert\Range([
                        'min' => 0,
                        'max' => 20,
                        'notInRangeMessage' => 'La note doit être comprise entre 0 et 20.'
                    ])
                ]
            ])
            ->add('dateCreation', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez renseigner une date.']),
                    new Assert\Type(['type' => '\DateTimeInterface', 'message' => 'La date doit être valide.'])
                ]
            ])
            ->add('appreciation', TextType::class, [
                'label' => 'Appréciation',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'appréciation est requise."]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => "L'appréciation ne peut pas dépasser 255 caractères."
                    ])
                ]
            ])
            ->add('Evaluation', EntityType::class, [
                'class' => Evaluation::class,
                'choice_label' => 'titre', // Assurez-vous que l'entité Evaluation a bien un champ `titre`
                'label' => 'Évaluation',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotNull(['message' => "Une évaluation doit être sélectionnée."])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resultat::class,
        ]);
    }
}