<?php

namespace App\DataFixtures;

use App\Entity\Faculty;
use App\Entity\Session;
use App\Entity\Subject;
use App\Repository\UserRepository;
use App\Repository\FacultyRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $subject = new Subject();
            $subject->setTitle('Example subject ' . $i);
            $subject->setSemester(1);
            $faculty = new Faculty();
            $subject->setFaculty($faculty);

            $manager->persist($subject);
        }

        // for ($i = 0; $i < 10; $i++) {
        //     $session = new Session();
        //     $session->setTitle('Example session ' . $i);
        //     $session->setDescription("Lorem");
        //     $session->setFaceToFace(1);
        //     $session->setLink('some/link.com');
        //     $session->setTutor($UR->findOneBy(['id'=>20]));
        //     $session->setSubject($subject);

        //     $manager->persist($session);
        // }

        $manager->flush();
    }
}
