<?php namespace Log;

use \Service\Session;

class AccessLog {
    public static function register() {
        $file = DOC_ROOT . 'app/Store/Log/' . date("Ymd") . '-accesslog.json';

        $user = (Session::get('s_username')) ? Session::get('s_username') : false;

        $accessLog[] = [
            'date'            => date('Y-m-d G:i:s'),
            'user'            => $user,
            'machine'         => gethostname() . '/' . REMOTE_ADDR,
            'request_method'  => REQUEST_METHOD,
            'request_uri'     => REQUEST_URI,
            'user_agent'      => HTTP_USER_AGENT,
            'data_post'       => $_POST,
            'data_get'        => $_GET,
            'data_request'    => $_REQUEST,
            'sessions'        => $_SESSION
        ];

        if(is_bool(DEBUG) && DEBUG === false) {
            if(file_exists($file) && $data = file_get_contents($file)) {
                $data = json_decode($data, true);
                $data = array_merge($data, $accessLog);
                file_put_contents($file, json_encode($data));
            } else {
                file_put_contents($file, json_encode($accessLog));
            }
        }
    }
}
