<?php

namespace Kernel;

use \Kernel\Model;
use \Routing\Router;
use \Service\Session;

class Controller
{
    public $model;
    public $config;

    public function __construct()
    {
        $_GET['lang'] ? Session::set('s_locale', $_GET['lang']) : false;

        $this->config = autoload_config();

        $this->model = new Model();
    }

    public function model($name)
    {
        $modelClass = ucfirst($name);
        $modelRoot = DOC_ROOT . 'app/Http/Models/' . $modelClass . EXT_PHP;

        if (file_exists($modelRoot)) {
            require $modelRoot;
            $model = '\\App\\Http\\Models\\'.$modelClass;
            $this->model = new $model();
        }
    }
}
