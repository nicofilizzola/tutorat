<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Faculty;
use App\Traits\getRoles;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use getRoles;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    private $validAndVerifiedCriteria = [
        'isValid' => 2,
        'isVerified' => true
    ];

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findFacultyTutors(Faculty $faculty){
        $tutors = [];
        $tutorRole = $this->getRoles()[1];
        foreach ($this->findBy(array_merge(
            ['faculty' => $faculty], 
            $this->validAndVerifiedCriteria
        )) as $user){
            if (in_array($tutorRole, $user->getRoles()) && array_search($tutorRole, $user->getRoles()) == count($user->getRoles()) - 1){
                array_push($tutors, $user);
            }
        }
        return $tutors;
    }
    public function findFacultyTutorEmails($faculty){
        $tutorEmails = [];
        foreach ($this->findBy(array_merge(['faculty' => $faculty], $this->validAndVerifiedCriteria)) as $user){
            if (in_array($this->getRoles()[1], $user->getRoles()) && !in_array($this->getRoles()[4], $user->getRoles())){
                array_push($tutorEmails, new Address($user->getEmail()));
            }
        }
        return $tutorEmails;  
    }

    public function findFacultyAdminEmails($faculty){
        $adminEmails = [];
        foreach ($this->findBy(array_merge(['faculty' => $faculty], $this->validAndVerifiedCriteria)) as $user){
            if (in_array($this->getRoles()[3], $user->getRoles()) && !in_array($this->getRoles()[4], $user->getRoles())){
                array_push($adminEmails, new Address($user->getEmail()));
            }
        }
        return $adminEmails;   
    }

    public function findFacultySecretaryEmail($faculty){
        $secretaryRole = $this->getRoles()[2];
        foreach ($this->findBy(array_merge(['faculty' => $faculty], $this->validAndVerifiedCriteria)) as $user){
            if (in_array($secretaryRole, $user->getRoles()) && array_search($secretaryRole, $user->getRoles()) == count($user->getRoles()) - 1){
                return new Address($user->getEmail());
            }
        }
    }

    public function findSessionJoinedStudentEmails($session) {
        $emails = [];
        foreach ($session->getStudents() as $student){
            array_push($emails, new Address($student->getEmail()));
        }
        return $emails;
    }


    public function findVerifiedUsersByFacultyNoSuperAdmin($faculty){
        $usersNoSuperAdmin = [];
        $users = $this->findBy([
            'isVerified' => 1,
            'faculty' => $faculty   
        ],
        ['isValid' => 'ASC']);  
        
        foreach($users as $user){
            if(!in_array($this->getRoles()[4], $user->getRoles())){
                $usersNoSuperAdmin[] = $user;
            }
        }
        
        return $usersNoSuperAdmin;
    }
}
