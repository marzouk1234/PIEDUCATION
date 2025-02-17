<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\DataTransformer\RolesDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Le nom doit contenir au moins 2 caractères.',
                        'maxMessage' => 'Le nom ne doit pas dépasser 20 caractères.'
                    ])
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 20,
                        'minMessage' => 'Le prénom doit contenir au moins 2 caractères.',
                        'maxMessage' => 'Le prénom ne doit pas dépasser 20 caractères.'
                    ])
                ],
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text', // Permet d'afficher un input HTML5 de type date
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer votre date de naissance.']),
                    new Assert\LessThan([
                        'value' => '-7 years',
                        'message' => 'Vous devez avoir au moins 7 ans.'
                    ]),
                    new Assert\GreaterThan([
                        'value' => '-60 years',
                        'message' => 'Vous devez avoir moins de 60 ans.'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire.']),
                    new Assert\Email(['message' => 'Veuillez entrer un email valide.']),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'Enseignant' => 'ROLE_ENS',
                    'Étudiant' => 'ROLE_ETU',
                ],
                'multiple' => false, // Un seul rôle sélectionnable
                'expanded' => false, // Affiché en dropdown
                'label' => 'Rôle',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'required' => false, // Le champ n'est plus requis
                'constraints' => [
                    new Assert\Length([
                        'min' => 8,
                        'max' => 20,
                        'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.',
                        'maxMessage' => 'Le mot de passe ne doit pas dépasser 20 caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/",
                        'message' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
                    ]),
                ],
            ]);

        // Appliquer le transformateur de données pour le champ roles
        $builder->get('roles')->addModelTransformer(new RolesDataTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
