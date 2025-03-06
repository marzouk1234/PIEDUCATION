<?php
// src/Form/FeedbackType.php

namespace App\Form;

use App\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_jeux', IntegerType::class, [
                'label' => 'ID du Jeu',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => "L'ID du jeu est obligatoire."]),
                    new Assert\Type(['type' => 'integer', 'message' => "L'ID du jeu doit être un nombre entier."])
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('feedback', TextareaType::class, [
                'label' => 'Feedback',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
