<?php

namespace App\Traits;

trait getRoles {
    private function getRoles(){
        return [
            'ROLE_STUDENT',
            'ROLE_TUTOR',
            'ROLE_SECRETARY',
            'ROLE_ADMIN',
            'ROLE_SUPERADMIN'
        ];
    }

    private function setRoles($index){
        $roles = [];
        for($i = 0; $i <= $index; $i++){
            $roles[] = $this->getRoles()[$i];
        }
        return $roles;
    }
}