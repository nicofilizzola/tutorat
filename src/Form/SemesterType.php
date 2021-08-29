<?php

namespace App\Form;

use App\Entity\Semester;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startYear', ChoiceType::class, [
                'choices' => [
                    date('Y', strtotime('-1 year')) => date('Y', strtotime('-1 year')),
                    date('Y') => date('Y'),
                    date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')),
                ]
            ])
            ->add('endYear', ChoiceType::class, [
                'choices' => [
                    date('Y') => date('Y'),
                    date('Y', strtotime('+1 year')) => date('Y', strtotime('+1 year')),
                    date('Y', strtotime('+2 year')) => date('Y', strtotime('+2 year')),
                ],
            ])
            ->add('yearOrder', ChoiceType::class, [
                'choices' => [
                    'S1' => 1,
                    'S2' => 2,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Semester::class,
        ]);
    }
}
