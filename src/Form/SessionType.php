<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Subject;
use App\Entity\Classroom;
use App\Repository\SubjectRepository;
use App\Repository\ClassroomRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SessionType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champs ne peut pas être vide."
                    ])
                ]
            ])
            ->add('description',  TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champs ne peut pas être vide."
                    ])
                ]
            ])
            ->add('faceToFace', ChoiceType::class, [
                'choices' => [
                    'Présentiel' => 1,
                    'Distanciel' => 2
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champs ne peut pas être vide."
                    ])
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => true
            ])
            ->add('link', TextType::class, [
                'constraints' => [
                    new Url([
                        "message" => "Ce lien est invalide"
                    ])
                ]
            ])
            ->add('classroom', EntityType::class, [
                'class' => Classroom::class,
                'choice_label' => "name",
                'query_builder' => function (ClassroomRepository $classroomRepository) {
                    return $classroomRepository->createQueryBuilder('c')
                        ->where('c.faculty = ' . $this->security->getUser()->getFaculty()->getId());
                }
            ]) // !faceToFace ? classroom == null
            ->add('subject', EntityType::class, [
                'class' => Subject::class,
                'query_builder' => function (SubjectRepository $subjectRepository) {
                    return $subjectRepository->createQueryBuilder('s')
                        ->where('s.faculty = ' . $this->security->getUser()->getFaculty()->getId());
                },
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champs ne peut pas être vide."
                    ])
                ]
            ])
            ->add('dateTime', DateTimeType::class, [
                'date_widget' => "single_text",
                'constraints' => [
                    new NotBlank([
                        'message' => "Ce champs ne peut pas être vide."
                    ]),
                    new GreaterThan([
                        'value' => 'today',
                        'message' => "Le cours doit être proposé au moins un jour avant"
                    ])
                ]
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
