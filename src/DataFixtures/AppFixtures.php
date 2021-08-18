<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Faculty;
use App\Entity\Session;
use App\Entity\Subject;
use App\Entity\Semester;
use App\Entity\AdminCode;
use App\Entity\Classroom;
use App\Repository\UserRepository;
use App\Repository\FacultyRepository;
use App\Traits\getRoles;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    use getRoles;

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
                    $subject->setTitle('Module ' . $h);
                    $subject->setShort('MO' . $h);
                    $subject->setSemester($j);
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
            $user->setRoles([
                $this->getRoles()[0],
                $this->getRoles()[1],
                $this->getRoles()[2],
                $this->getRoles()[3],
            ]);
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
            $user->setRoles([
                $this->getRoles()[0],
                $this->getRoles()[1],
                $this->getRoles()[2],
            ]);
            $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
            $user->setYear(4);
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
                $user->setRoles([$this->getRoles()[0]]);
                $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
                $user->setYear(1);
                $user->setIsValid(2);
                $user->setIsVerified(1);
                $user->updateTimestamp();
                $user->setFaculty($faculty);

                $manager->persist($user);

                $user = new User();
                $user->setFirstName('Tutor' . $j);
                $user->setLastName($i);
                $user->setEmail('tutor'. $j . $i .'@iut-tarbes.fr');
                $user->setRoles([
                    $this->getRoles()[0],
                    $this->getRoles()[1],
                ]);
                $user->setPassword('$2y$13$xNGMwdug4hU09AaUI1kRS.qKTt9oTpwY9iU5aKiasjvR55l7ltk6m');
                $user->setYear(1);
                $user->setIsValid(2);
                $user->setIsVerified(1);
                $user->updateTimestamp();
                $user->setFaculty($faculty);

                $manager->persist($user);
            }
            
            $semester = new Semester;
            $semester->setStartYear("2021");
            $semester->setEndYear("2022");
            $semester->setYearOrder(1);
            $semester->setFaculty($faculty);

            $manager->persist($semester);
        }

        $adminCode = new AdminCode();
        $adminCode->setCode('1234');
        $manager->persist($adminCode);

        $manager->flush();
    }
}
