<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Faculty;
use App\Traits\emailRegex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    use emailRegex;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, [
                'label' => 'Prénom',
                'constraints' => [
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ]+$/i",
                        "message" => "Le nom renseigné est invalide."
                    ]),
                    new NotBlank([
                        'message' => "Veuillez entrer un prénom"
                    ])
                ]
            ])
            ->add('lastName', null, [
                'label' => 'Nom',
                'constraints' => [
                    new Regex([
                        'pattern' => "/^[A-Za-zÀ-ÖØ-öø-ÿ]+$/i",
                        "message" => "Le prénom renseigné est invalide."
                    ]),
                    new NotBlank([
                        'message' => "Veuillez entrer un nom"
                    ])
                ]
            ])
            ->add('email', null, [
                // 'mapped' => false,
                'label' => 'Adresse email universitaire',
                'constraints' => [
                    new Regex([
                        'pattern' => $this->getEmailRegex(),
                        "message" => "L'adresse email renseignée est invalide."
                    ]),
                    new NotBlank([
                        'message' => "Veuillez entrer une adresse email"
                    ]),
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => "Rôle",
                'choices' => [
                        'Étudiant' => 1,
                        'Étudiant tuteur' => 2,
                        'Administrateur' => 4
                ],
                'mapped' => false,
                'constraints' => [
                    new NotEqualTo([
                        'value' => 3,
                        'message' => "Valeur invalide"
                    ])
                ]

            ])
            ->add('adminCode', PasswordType::class, [
                'label' => "Insérez le code d'administrateur",
                'mapped' => false
            ])
            ->add('year', ChoiceType::class, [
                'label' => "Année",
                'choices' => [
                        '1ère année' => 1,
                        '2ème année' => 2,
                        '3ème année' => 3
                ],
            ])
            ->add('faculty', EntityType::class, [
                'label' => "Département d'enseignement",
                'class' => Faculty::class,
                'choice_label' => 'Name'
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'first_options' => ['label' => 'Mot de passe', 'attr' => ['class' => 'hoveredItems']],
                'second_options' => ['label' => 'Vérifier le mot de passe', 'attr' => ['class' => 'hoveredItems']]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "Accepter les conditions d'utilisation",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
