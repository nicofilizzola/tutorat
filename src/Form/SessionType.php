<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Subject;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use App\Repository\SubjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
                'class' => Subject::class,
                'choice_label' => 'title',
                'query_builder' => function (SubjectRepository $subjectRepository) {
                    return $subjectRepository->createQueryBuilder('s')
                        ->where('s.faculty = ' . $this->security->getUser()->getFaculty()->getId());
                }
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
