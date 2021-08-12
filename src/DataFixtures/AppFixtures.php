<?php

namespace App\DataFixtures;

use App\Entity\AdminCode;
use App\Entity\User;
use App\Entity\Faculty;
use App\Entity\Session;
use App\Entity\Subject;
use App\Entity\Classroom;
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

            $user = new User();
            $user->setFirstName('Admin');
            $user->setLastName($i);
            $user->setEmail('admin'. $i .'@iut-tarbes.fr');
            $user->setRoles(["ROLE_STUDENT","ROLE_TUTOR","ROLE_SECRETARY","ROLE_ADMIN"]);
            $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
            $user->setYear(4);
            $user->setIsValid(2);
            $user->setIsVerified(1);
            $user->updateTimestamp();
            $user->setFaculty($faculty);

            $manager->persist($user);

            $user = new User();
            $user->setFirstName('Secretary');
            $user->setLastName($i);
            $user->setEmail('secretary'. $i .'@iut-tarbes.fr');
            $user->setRoles(["ROLE_STUDENT","ROLE_TUTOR","ROLE_SECRETARY"]);
            $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
            $user->setYear(4);
            $user->setIsValid(2);
            $user->setIsVerified(1);
            $user->updateTimestamp();
            $user->setFaculty($faculty);

            $manager->persist($user);

            $user = new User();
            $user->setFirstName('Tutor');
            $user->setLastName($i);
            $user->setEmail('tutor'. $i .'@iut-tarbes.fr');
            $user->setRoles(["ROLE_STUDENT","ROLE_TUTOR"]);
            $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
            $user->setYear(1);
            $user->setIsValid(2);
            $user->setIsVerified(1);
            $user->updateTimestamp();
            $user->setFaculty($faculty);

            $manager->persist($user);

            for ($j = 0; $j < 5; $j++) {
                $user = new User();
                $user->setFirstName('User' . $j);
                $user->setLastName('Fac' . $i);
                $user->setEmail('user'. $j . $i .'@iut-tarbes.fr');
                $user->setRoles(["ROLE_STUDENT"]);
                $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
                $user->setYear(1);
                $user->setIsValid(2);
                $user->setIsVerified(1);
                $user->updateTimestamp();
                $user->setFaculty($faculty);

                $manager->persist($user);
            }            
        }

        $adminCode = new AdminCode();
        $adminCode->setCode('1234');
        $manager->persist($adminCode);

        $manager->flush();
    }
}
