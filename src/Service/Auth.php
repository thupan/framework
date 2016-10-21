<?php

namespace Service;

use Service\Session;
use Service\Redirect;

class Auth {
    public static function attempt($data = [])
    {
        //
    }

    public static function check()
    {
    }

    public static function connection($username = null, $password = null, $environment = 'dev', $connection = null)
    {
        // se não for passado o usuário ou a senha, então busca pelo arquivo de configuração.
        if(!$username || !$password) {
            $config = autoload_config();

            $connection = is_null($connection) ? $config['database']['DB_DEFAULT_CONN'] : $connection;

            foreach($config['database']['connections'][$environment] as $key) {
                if($key['connection'] === $connection) {
                    $username = $key['username'];
                    $password = $key['password'];
                }
            }
        }

        Session::destroy();
        Session::set('s_username', $username);
        Session::set('s_password', base64_encode($password));
        Session::set('s_environment', $environment);

        $database = new \Database\Database();

        if ($error = $database->getInstance()->getError()) {
            return $error;
        } else {
            Session::set('s_loggedIn', Session::get('s_token').md5($username.$password));
            return false;
        }
    }

    public static function login()
    {

    }

    public static function validate()
    {
    }

    public static function logout($redirect = '/')
    {
        Session::destroy();
        Redirect::to($redirect);
    }
}
