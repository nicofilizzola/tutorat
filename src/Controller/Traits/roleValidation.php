<?php

namespace App\Controller\Traits;

trait roleValidation {
    private function isSecretary(){
        $userRoles = $this->getUser() ? $this->getUser()->getRoles() : null;
        if (
            !$this->getUser() ||
            !in_array($this->getRoles()[2], $userRoles) || 
            // in_array($this->getRoles()[3], $userRoles) || 
            $this->getUser()->getIsValid() !== 2 || 
            !$this->getUser()->isVerified()
        ){
            return false;
        }
        return true;
    }
}