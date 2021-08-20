<?php

namespace App\Controller\Traits;

trait isVerifiedUser{
    private function isVerifiedUser(){
        return !$this->getUser() || !$this->getUser()->isVerified() ? false : true;
    }
}