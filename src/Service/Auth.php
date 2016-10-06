<?php

namespace Service;

use \Service\Session;
use \Service\Redirect;

class Auth {
    public static function attempt($data = []) {
        //
    }

    public static function check() {

    }

    public static function validade() {

    }

    public static function login() {

    }

    public static function logout() {
        Session::destroy();
        Redirect::to('/');
    }
}
