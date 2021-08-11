<?php

namespace App\Controller\Requires;

use App\Entity\User;
use App\Repository\SessionRepository;

function getFacultySessions(SessionRepository $sessionRepository, array $criteria, User $user){
    $allSessions = $sessionRepository->findBy($criteria, ['id' => 'ASC']);
    $facultySessions = [];
    foreach ($allSessions as $session) {
        if ($session->getSubject()->getFaculty() == $user->getFaculty()){
            array_push($facultySessions, $session);
        }
    }
    return $facultySessions;
}