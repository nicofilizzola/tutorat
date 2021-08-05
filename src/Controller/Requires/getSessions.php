<?php

use App\Entity\User;
use App\Entity\Session;
use App\Repository\SessionRepository;

function getSessions(SessionRepository $sessionRepository, array $criteria, User $user, Session $except = null){
    $allSessions = $sessionRepository->findBy($criteria, ['id' => 'ASC']);
    $facultySessions = [];
    foreach ($allSessions as $session) {
        if ($session->getSubject()->getFaculty() == $user->getFaculty()){
            array_push($facultySessions, $session);
        }
    }
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
        if ($session == $except){
            unset($sessionsAfterToday[$i]);
            return array_values($sessionsAfterToday);
        }
    }
}