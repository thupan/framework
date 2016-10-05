<?php

namespace Service;

use \Kernel\View;

class Response
{
    public static function error($number, $message = false)
    {
        View::assign('message', $message);
        View::render('@templates/Error/' . $number);
    }

    public static function json($data)
    {
        return json_encode($data);
    }
}
