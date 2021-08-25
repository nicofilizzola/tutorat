<?php

namespace App\Form;

use App\Entity\Faculty;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FacultyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        function generateRandomString($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        $builder
            ->add('name')
            ->add('short')
            ->add('code', null, [
                'data' => generateRandomString(),
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('adminFirstName', TextType::class, [
                'mapped' => false
            ])
            ->add('adminLastName', TextType::class, [
                'mapped' => false
            ])
            ->add('adminPassword', TextType::class, [
                'mapped' => false,
                'data' => generateRandomString(),
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('adminEmail', TextType::class, [
                'mapped' => false
            ])
            ->add('secretaryEmail', TextType::class, [
                'mapped' => false
            ])
            ->add('secretaryPassword', TextType::class, [
                'mapped' => false,
                'data' => generateRandomString(),
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('startYear', ChoiceType::class, [
                'choices' => [
                    date('Y', strtotime('-1 year')) => date('Y', strtotime('-1 year')),
                    date('Y') => date('Y'),
                    date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')),
                ],
                'mapped' => false
            ])
            ->add('endYear', ChoiceType::class, [
                'choices' => [
                    date('Y') => date('Y'),
                    date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')),
                    date('Y', strtotime('+2 year')) => date('Y', strtotime('+2 year')),
                ],
                'mapped' => false
            ])
            ->add('semesterYearOrder', ChoiceType::class, [
                'choices' => [
                    'S1' => 1,
                    'S2' => 2,
                ],
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Faculty::class,
        ]);
    }
}
