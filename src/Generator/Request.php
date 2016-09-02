<?php

namespace Generator;

class Request {

    protected $_controller  = null;
    protected $_method      = null;
    protected $_args        = [];

    protected $_http_method = null;

    public function __construct() {
        $parts = explode('/', $_SERVER['PHP_SELF']);

        $index_pos = array_search("index.php", $parts);

        if ($index_pos) {
            for ($i = 0; $i <= $index_pos; $i++) {
                unset($parts[$i]);
            }
        }

        $parts = array_filter($parts);

        $this->_controller = array_shift($parts);
        $this->_method     = array_shift($parts);
        $this->_args       = isset($parts) ? $parts : [];

        $this->_http_method = $_SERVER['REQUEST_METHOD'];

        $params = explode('?', $_SERVER['REQUEST_URI'])[1];
        $params = explode('&', $params);


    }

    public static function getMethod() {
        foreach(new self as $key => $value) {
            if($key === '_method') {
                return $value;
            }
        }
    }

    public static function getArgs() {
        foreach(new self as $key => $value) {
            if($key === '_args') {
                return $value;
            }
        }
    }

    public static function getController() {
        foreach(new self as $key => $value) {
            if($key === '_controller') {
                return $value;
            }
        }
    }
}
