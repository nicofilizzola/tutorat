<?php

use App\Entity\User;
use App\Repository\SessionRepository;

function getSessions(SessionRepository $sessionRepository, User $user){
    $allSessions = $sessionRepository->findBy(['isValid' => true], ['id' => 'ASC']);
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
    return $sessionsAfterToday;
}