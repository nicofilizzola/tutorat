<?php

namespace App\DataFixtures;

use App\Entity\Classroom;
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
            $faculty = new Faculty();
            $faculty->setName('Département ' . $i);
            $faculty->setShort('D' . $i);

            $manager->persist($faculty);

            for ($j = 0; $j < 3; $j++) {
                for ($h = 0; $h < 3; $h++) {
                    $subject = new Subject();
                    $subject->setTitle('Module ' . $i);
                    $subject->setShort('MO' . $i);
                    $subject->setSemester($h + 1);
                    $subject->setFaculty($faculty);
        
                    $manager->persist($subject);
                }
            }

            for ($j = 0; $j < 3; $j++) {
                $classroom = new Classroom();
                $classroom->setName($i . "0" . $j);
                $classroom->setFaculty($faculty);

                $manager->persist($classroom);
            }
        }

        $manager->flush();
    }
}
