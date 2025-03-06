<?php

namespace App\Form;

use App\Entity\Inscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateInscription', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(), // Sets the default date to the current date and time
                'disabled' => true, // Make the field non-editable
                'html5' => true,
                'attr' => ['class' => 'form-control']

            ]);
            /*
            ->add('plan', ChoiceType::class, [
                'choices' => [
                    '1 mois' => '1 mois',
                    '3 mois' => '3 mois',
                    '6 mois' => '6 mois',
                    '1 an' => '1 an',
                ],
                'placeholder' => 'Sélectionner une durée',
                'attr' => ['class' => 'form-control']
            ]);
            */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
