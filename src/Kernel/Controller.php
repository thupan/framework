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
        $this->config = autoload_config();
        $this->model  = new Model();

        if($_GET['lang']) {
            $languages = scandir(DOC_ROOT . 'app/Language');
            if(in_array($_GET['lang'], $languages)) {
                Session::set('s_locale', $_GET['lang']);
            } else {
                \Service\Debug\Debug::message('não foi possível localizar o idioma ' . $_GET['lang'] . '. Seu idioma não foi alterado.');
            }
        }
    }

    public function model($name)
    {
        $modelClass = ucfirst($name);
        $modelRoot = DOC_ROOT . 'app/Http/Models/' . $modelClass . EXT_PHP;

        if (file_exists($modelRoot)) {
            require $modelRoot;
            $model = '\\App\\Http\\Models\\' . $modelClass;
            $this->model = new $model();
        }
    }
}
