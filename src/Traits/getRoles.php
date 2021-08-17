<?php

namespace App\Traits;

trait getRoles {
    public function getRoles(){
        return [
            'ROLE_STUDENT',
            'ROLE_TUTOR',
            'ROLE_SECRETARY',
            'ROLE_ADMIN'
        ];
    }
}