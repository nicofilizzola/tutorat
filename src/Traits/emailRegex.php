<?php

namespace App\Traits;

trait emailRegex {
    private function getEmailRegex() {
        return "/^[a-zA-Z]+[.][a-zA-Z]+@iut-tarbes.fr$/i";
    }
}