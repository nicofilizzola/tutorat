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

    public function findByStudentAwaiting(User $user){
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
            $session = $this->findOneBy(['id' => $sessionId]);
            if (empty($session->getParticipants())){
                array_push($sessions, $session);
            }
        }
        
        return $sessions;
    }

    public function findByFaculty(Faculty $faculty, array $criteria, bool $idAsc = true){
        $allSessions = $this->findBy(
            $criteria, 
            ['id' => $idAsc ? 'ASC' : 'DESC']);
        $facultySessions = [];
        foreach ($allSessions as $session) {
            if ($session->getSubject()->getFaculty() == $faculty){
                array_push($facultySessions, $session);
            }
        }
        return $facultySessions;
    }

    public function findByFacultyAfterToday(Faculty $faculty, array $criteria, Session $except = null, $onlyValidated = null){
        $facultySessions = $this->findByFaculty($faculty, $criteria);

        $sessionsAfterToday = [];
        foreach ($facultySessions as $session) {
            if (date('Y-m-d h:i:s', strtotime('+1 hour')) < date('Y-m-d h:i:s', $session->getDateTime()->getTimestamp())){
                array_push($sessionsAfterToday, $session);
            }
        }

        if (!is_null($except)){
            for ($i = 0; $i < count($sessionsAfterToday); $i++) {
                if ($sessionsAfterToday[$i] == $except){
                    unset($sessionsAfterToday[$i]);
                    $sessionsAfterToday =  array_values($sessionsAfterToday);
                }
            }
        }

        if (!is_null($onlyValidated)){
            for ($i = 0; $i < count($sessionsAfterToday); $i++) {
                $unsetCondition = $onlyValidated == false ? !empty($sessionsAfterToday[$i]->getParticipants()) : empty($sessionsAfterToday[$i]->getParticipants());
                if ($unsetCondition){
                    unset($sessionsAfterToday[$i]);
                }
            }
        }

        return $sessionsAfterToday;    
    }

    public function findThreeBySessionSubject(Session $session){
        $allSessions = $this->findByFacultyAfterToday(
            $session->getSubject()->getFaculty(),
            [
                'isValid' => true,
                'subject' => $session->getSubject()
            ],
            $session
        );

        $awaitingSessions = []; 
        foreach ($awaitingSessions as $session) {
            if (empty($session->getParticipants)){
                $awaitingSessions[] = $session;
            }
        }

        if (!is_null($awaitingSessions) && count($awaitingSessions) >= 3){
            $sessions = [];
            for ($i = 0; $i < 2; $i++){
                array_push($sessions, $awaitingSessions[$i]);
            }
        } else {
            $sessions = $awaitingSessions;
        }

        return $sessions;
    }

    public function findByJoinedSessions(SemesterRepository $semesterRepository, $user){
        $semesterSessions = $this->findBy([
            'semester' => $semesterRepository->findCurrentFacultySemester($user->getFaculty())
        ]);
        $joinedSessions = [];
        foreach ($semesterSessions as $session){
            if (in_array($user, $session->getStudents()->toArray())){
                array_push($joinedSessions, $session);
            }
        }
        return $joinedSessions;
    }
}
