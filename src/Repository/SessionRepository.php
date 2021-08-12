<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Faculty;
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

    public function findFacultySessions(Faculty $faculty, array $criteria){
        $allSessions = $this->findBy($criteria, ['id' => 'ASC']);
        $facultySessions = [];
        foreach ($allSessions as $session) {
            if ($session->getSubject()->getFaculty() == $faculty){
                array_push($facultySessions, $session);
            }
        }
        return $facultySessions;
    }

    public function findFacultySessionsAfterToday(Faculty $faculty, array $criteria, Session $except = null){
        $facultySessions = $this->findFacultySessions($faculty, $criteria);

        $sessionsAfterToday = [];
        foreach ($facultySessions as $session) {
            if (date('Y-m-d h:i:s', strtotime('+1 hour')) < date('Y-m-d h:i:s', $session->getDateTime()->getTimestamp())){
                array_push($sessionsAfterToday, $session);
            }
        }

        if (is_null($except)){
            return $sessionsAfterToday;
        }

        for ($i = 0; $i < count($sessionsAfterToday); $i++) {
            if ($sessionsAfterToday[$i] == $except){
                unset($sessionsAfterToday[$i]);
                return array_values($sessionsAfterToday);
            }
        }
    }

    public function findThreeSubjectRelatedSessions(Session $session){
        $allSessions = $this->findFacultySessionsAfterToday(
            $session->getSubject()->getFaculty(),
            [
                'isValid' => true,
                'subject' => $session->getSubject()
            ],
            $session
        );

        if (!is_null($allSessions) && count($allSessions) >= 3){
            $sessions = [];
            for ($i = 0; $i < 2; $i++){
                array_push($sessions, $allSessions[$i]);
            }
        } else {
            $sessions = $allSessions;
        }

        return $sessions;
    }
}
