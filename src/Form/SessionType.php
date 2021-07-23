<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
            ])
            ->add('description',  TextareaType::class, [

            ])
            ->add('faceToFace', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 1,
                    'Distanciel' => 2
                ]
            ])
            ->add('link')
            ->add('classroom')
            ->add('subject', EntityType::class, [
                // 'class' => Subject::class,
                // 'choice_label' => 'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
