<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SuperAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => [
                    'label' => 'email'
                ],
                'second_options' => [
                    'label' => 'repeat email'
                ],
                'invalid_message' => "emails don't match"
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'pwd'
                ],
                'second_options' => [
                    'label' => 'repeat pwd'
                ],
                'invalid_message' => "pwds don't match"
            ])
            ->add('year', NumberType::class, [
                'data' => 4,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('isValid', NumberType::class, [
                'data' => 2,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('isVerified', NumberType::class, [
                'data' => 1,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('firstName', TextType::class, [
                'data' => 'Super',
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('lastName', TextType::class, [
                'data' => "Admin",
                'attr' => [
                    'readonly' => true
                ]
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
