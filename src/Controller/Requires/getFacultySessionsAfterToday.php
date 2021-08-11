<?php

use App\Entity\User;
use App\Entity\Session;
use App\Repository\SessionRepository;
use App\Controller\Requires\getFacultySessions;

function getFacultySessionsAfterToday(SessionRepository $sessionRepository, array $criteria, User $user, Session $except = null){
    $facultySessions = getFacultySessions($sessionRepository, $criteria, $user);

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