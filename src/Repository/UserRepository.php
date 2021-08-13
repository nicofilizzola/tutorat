<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Faculty;
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
        $facultyUsers = $this->findBy([
            'faculty' => $faculty,
            ...$this->validAndVerifiedCriteria
        ]);
        $tutors = [];
        foreach ($facultyUsers as $user){
            if (in_array("ROLE_TUTOR", $user->getRoles()) && !in_array("ROLE_ADMIN", $user->getRoles())){
                array_push($tutors, $user);
            }
        }
        return $tutors;
    }

    public function findFacultyAdminEmails($faculty){
        $users = $this->findBy([
            'faculty' => $faculty,
            ...$this->validAndVerifiedCriteria
        ]);

        $adminEmails = [];
        foreach ($users as $user){
            if (in_array("ROLE_ADMIN", $user->getRoles())){
                array_push($adminEmails, $user->getEmail());
            }
        }
        return $adminEmails;   
    }

    public function findFacultySecretaryEmail($faculty){
        foreach ($this->findBy([
            'faculty' => $this->getUser()->getFaculty(),
            ...$this->validAndVerifiedCriteria
            ]) as $user){
            if (in_array("ROLE_SECRETARY", $user->getRoles()) && !in_array("ROLE_ADMIN", $user->getRoles())){
                return $user->getEmail();
            }
        }
    }
}
