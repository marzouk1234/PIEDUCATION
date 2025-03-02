<?php

namespace App\Form\Search;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    /**
     * Construit le formulaire de recherche.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'required' => false,
                'label' => 'Sujet',
                'attr' => [
                    'placeholder' => 'Rechercher par sujet...',
                ],
            ])
            ->add('contenu', TextType::class, [
                'required' => false,
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Rechercher par contenu...',
                ],
            ])
            ->add('auteur', TextType::class, [
                'required' => false,
                'label' => 'Auteur',
                'attr' => [
                    'placeholder' => 'Rechercher par auteur...',
                ],
            ])
            ->add('datePub', DateType::class, [
                'required' => false,
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Rechercher par date...',
                ],
            ]);
    }

    /**
     * Configure les options du formulaire.
     *
     * @param OptionsResolver $resolver Le résolveur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
