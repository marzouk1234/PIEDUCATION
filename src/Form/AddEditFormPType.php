<?php

namespace App\Form;

use App\Entity\FormP;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEditFormPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Saisissez le contenu...',
                    'rows' => 5,
                ],
            ])
            ->add('date_pub', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => (new \DateTime())->format('Y-m-d'),
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le sujet...',
                ],
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de l’auteur...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormP::class,
        ]);
    }
  

}
