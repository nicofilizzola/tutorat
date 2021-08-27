<?php

namespace App\Controller\Traits;

// requires getRoles in controller
trait roleValidation {
    private function isSecretary($onlySecretary = null){
        $userRoles = $this->getUser() ? $this->getUser()->getRoles() : null;
        $condition = !$this->getUser() ||
            !in_array($this->getRoles()[2], $userRoles) || 
            $this->getUser()->getIsValid() !== 2 || 
            !$this->getUser()->isVerified();
        $condition = $onlySecretary ? $condition || in_array($this->getRoles()[3], $userRoles) : $condition;
         
        if ($condition){
            return false;
        }
        return true;
    }
}