<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Session;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    // /**
    //  * @return Session[] Returns an array of Session objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Session
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findUserSession(User $user){
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT session.id FROM user, session, session_user
            WHERE user.id = :userId
            AND user.id = session_user.user_id
            AND session.id = session_user.session_id
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $user->getId()]);

        $sessionIds = $stmt->fetchAllAssociative();

        $sessions = [];
        foreach ($sessionIds as $sessionId){
            array_push($sessions, $this->findOneBy(['id' => $sessionId]));
        }
        
        return $sessions;
    }
}
