<?php

namespace Kernel;

use \Exception;
use \Service\Debug\Debug;
use \Service\Session;
use \Routing\Router;
use \Service\XHR;

class View
{
    protected $twig;
    protected $twig_loader;

    protected static $data     = [];
    protected static $instance = false;

    public static $config = false;
    public static $title  = false;

    public static $functions = [];
    public static $filters   = [];

    public static function getInstance()
    {
        self::$config = autoload_config();

        if (!self::$instance) {
            try {
                $controller = Router::getControllerName();

                $loader = new \Twig_Loader_Filesystem([
                    self::$config['app']['TWIG_VIEWS'],
                ]);

                $loader->addPath(self::$config['app']['TWIG_VIEWS'], 'view');
                $loader->addPath(__DIR__ . DS . '../Support/Templates/', 'stemplates');
                $loader->addPath(DOC_ROOT . 'app/Support/Templates/', 'templates');

                self::$instance = new \Twig_Environment($loader, [
                    'cache' => self::$config['app']['TWIG_CACHE'],
                    'auto_reload' => self::$config['app']['TWIG_AUTO_RELOAD'],
                    'autoescape' => self::$config['app']['TWIG_AUTO_ESCAPE'],
                    'debug' => self::$config['app']['DEBUG'],
                ]);

                Debug::collectorTwig(self::$instance);

                self::$functions[] = new \Twig_SimpleFunction('load_modals', function() {
                    foreach(glob(DOC_ROOT . 'app/Http/Views/_modals/modal.*.twig') as $modal) {
                        if(file_exists($modal)) {
                            require $modal;
                        }
                    }
                });

                self::$functions[] = new \Twig_SimpleFunction('load_js', function() {
                    $default_methods = ['index', 'novo', 'edit', 'detail'];
                    $files_loaded    = [];
                    $scripts         = [];

                    // verifica a tela atual pelo método do controlador
                    if(in_array(Router::getMethod(), $default_methods)) {
                        // verifica se o script deste metodo existe
                        if( file_exists($script = DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/js/' . Router::getMethod() . '.js') ) {
                            // explode o path do script
                            $file_name = explode('/', $script);
                            // pega apenas o nome do script ignorando a extenção
                            $file_name = explode('.', end($file_name))[0];
                            // verifica se o arquivo ja foi carregado
                            if(!in_array($file_name, $files_loaded)) {
                                $scripts[] = "\n<!-- autoloaded $file_name -->\n<script type='text/javascript'>". \Service\Minifier::minify(file_get_contents($script)) ."</script>\n";
                                $files_loaded[] = $file_name;
                            }
                        }

                        // pega todos os filhos existentes da tela atual
                        foreach(glob(DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/js/' . Router::getMethod() . '-*.js') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<script type='text/javascript'>". \Service\Minifier::minify(file_get_contents($script)) ."</script>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }

                        // pega todos os scripts restantes do controlador
                        foreach(glob(DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/js/*.js') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se é filho da tela
                                $child     = explode('-', $file_name);
                                // se não for filho, ignora o script
                                if(count($child) >= 2) {
                                    if($child[0] != Router::getMethod()) continue;
                                }
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded) && !in_array($file_name, $default_methods)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<script type='text/javascript'>". \Service\Minifier::minify(file_get_contents($script)) ."</script>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }

                        // carrega todos os scripts do public
                        foreach(glob(DOC_ROOT . 'public/js/*.js') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<script type='text/javascript'>". \Service\Minifier::minify(file_get_contents($script)) ."</script>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }
                    }

                    echo implode("\n", $scripts);
                });

                self::$functions[] = new \Twig_SimpleFunction('load_css', function() {
                    $default_methods = ['index', 'novo', 'edit', 'detail'];
                    $files_loaded    = [];
                    $scripts         = [];

                    // verifica a tela atual pelo método do controlador
                    if(in_array(Router::getMethod(), $default_methods)) {
                        // verifica se o script deste metodo existe
                        if( file_exists($script = DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/css/' . Router::getMethod() . '.css') ) {
                            // explode o path do script
                            $file_name = explode('/', $script);
                            // pega apenas o nome do script ignorando a extenção
                            $file_name = explode('.', end($file_name))[0];
                            // verifica se o arquivo ja foi carregado
                            if(!in_array($file_name, $files_loaded)) {
                                $scripts[] = "\n<!-- autoloaded $file_name -->\n<style type='text/css'>". \Service\Minifier::minify(file_get_contents($script)) ."</style>\n";
                                $files_loaded[] = $file_name;
                            }
                        }

                        // pega todos os filhos existentes da tela atual
                        foreach(glob(DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/css/' . Router::getMethod() . '-*.css') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<style type='text/css'>". \Service\Minifier::minify(file_get_contents($script)) ."</style>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }

                        // pega todos os scripts restantes do controlador
                        foreach(glob(DOC_ROOT . 'app/Http/Views/' . Router::getControllerName() . '/css/*.css') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se é filho da tela
                                $child     = explode('-', $file_name);
                                // se não for filho, ignora o script
                                if(count($child) >= 2) {
                                    if($child[0] != Router::getMethod()) continue;
                                }
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded) && !in_array($file_name, $default_methods)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<style type='text/css'>". \Service\Minifier::minify(file_get_contents($script)) ."</style>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }

                        // carrega todos os scripts do public
                        foreach(glob(DOC_ROOT . 'public/css/*.css') as $script) {
                            // verifica se o script deste metodo existe
                            if( file_exists($script) ) {
                                // explode o path do script
                                $file_name = explode('/', $script);
                                // pega apenas o nome do script ignorando a extenção
                                $file_name = explode('.', end($file_name))[0];
                                // verifica se o arquivo ja foi carregado
                                if(!in_array($file_name, $files_loaded)) {
                                    $scripts[] = "\n<!-- autoloaded $file_name -->\n<style type='text/css'>". \Service\Minifier::minify(file_get_contents($script)) ."</style>\n";
                                    $files_loaded[] = $file_name;
                                }
                            }
                        }
                    }

                    echo implode("\n", $scripts);
                });

                self::$functions[] = new \Twig_SimpleFunction('header', function($url, $buttons, $validate) {
                    echo \Service\HTML\Table::formHeader($url, $buttons, $validate);
                });

                self::$functions[] = new \Twig_SimpleFunction('table', function($id, $fields = [], $actions = []) {
                    //NOME:TABELA.CAMPO:TIPO_PESQUISA

                    foreach($fields as $index => $key) {
                        if(is_array($key)) {
                            $value = array_values($key)[0];
                            $key   = array_keys($key)[0];

                            if($value) {
                                $data_value = '@' . base64_encode(json_encode($value));
                            }

                            $k = explode(':', $key);
                            $arr[$k[0]] = $k[1].':'.$k[2].'@select2' . $data_value;
                        } else {
                            $k = explode(':', $key);
                            $arr[$k[0]] = $k[1].':'.$k[2];
                        }
                    }

                    \Service\HTML\Table::formSearch($id);

                    \Service\HTML\Table::Open();
                    \Service\HTML\Table::Header([], $id, $arr, $actions);

                    if($id != 'tabela') {
                        $id = 'tabela-'.$id;
                    }

                    \Service\HTML\Table::Body(['id' => $id]);

                    return \Service\HTML\Table::Close();
                });

                self::$functions[] = new \Twig_SimpleFunction('formSearch', function($id = null, $url = null, $actions = [], $validate = true) {
                    return \Service\HTML\Form::formSearch($id, $url, $actions, $validate);
                });

                self::$functions[] = new \Twig_SimpleFunction('alert', function($message, $alert = 'info') {
                    return XHR::alert($message, $alert);
                });

                self::$functions[] = new \Twig_SimpleFunction('dd', function($var) {
                    return dd($var);
                });

                self::$functions[] = new \Twig_SimpleFunction('validate', function($array = [], $key = null) {
                    if(in_array($key, $array)) {
                        return '<span style="color:red">' . self::$config[Session::get('s_locale')]['app']['requiredMsg'] . '</span>';
                    }
                });

                self::$functions[] = new \Twig_SimpleFunction('select2_options', function($array = [], $var = null) {
                    if($array) {
                        $options = "<option value=''></option>";
                        foreach($array as $index) {
                                $selected = ($index['ID'] == $var) ? ' selected="selected" ' : false;
                                $options .= "<option value='{$index['ID']}' $selected>{$index['TEXT']}</option>";
                        }
                    }

                    return ($options) ? $options : '<option selected>ERRO: não houve retorno do array no select2_options</option>';
                });

                foreach (self::$functions as $key => $function) {
                    self::$instance->addFunction($function);
                }

                foreach (self::$filters as $key => $filter) {
                    self::$instance->addFilter($filter);
                }

                $lexer = new \Twig_Lexer(self::$instance, [
                    'tag_block' => self::$config['app']['TWIG_TAG_BLOCK'],
                    'tag_variable' => self::$config['app']['TWIG_TAG_VARIABLE'],
                    'tag_comment' => self::$config['app']['TWIG_TAG_COMMENT'],
                    'interpolation' => self::$config['app']['TWIG_TAG_INTERPOLATION'],
                ]);

                self::$instance->setLexer($lexer);

                /*
                 * Linguagem da aplicação!
                 *
                 * Se existir um script de idioma para o controlador ativado, carregará na memória!
                 */
                 // global
                 self::$instance->addGlobal('langApp', self::$config[Session::get('s_locale')]['app']);
                // do programa
                self::$instance->addGlobal('lang', self::$config[Session::get('s_locale')][$controller]);

                /*
                 * Variáveis Globais
                 *
                 * São essenciais para o funcionamento basico da aplicação.
                 */
                self::$instance->addGlobal('hostname',      gethostname());

                self::$instance->addGlobal('URL',           URL);
                self::$instance->addGlobal('bower_dir',     URL . self::$config['app']['BOWER_COMPONENTS']);


                // controle de versao em template
                $version = DOC_ROOT . '/VERSION.txt';

                if(file_exists($version)) {
                    $version = file_get_contents($version);
                    self::$instance->addGlobal('thupan_version', $version);
                }
                // fim do controle de versao em template

                self::$instance->addGlobal('app_name',      self::$config['app']['APP_NAME']);
                self::$instance->addGlobal('app_title',      self::$config['app']['APP_TITLE']);
                self::$instance->addGlobal('app_version',   self::$config['app']['APP_VERSION']);
                self::$instance->addGlobal('theme',         self::$config['app']['DEFAULT_THEME']);
                self::$instance->addGlobal('page_lang',     self::$config['app']['TWIG_PAGE_LANG']);
                self::$instance->addGlobal('page_charset',  self::$config['app']['TWIG_PAGE_CHARSET']);
                self::$instance->addGlobal('debug',         (autoload_machines()) ? false : self::$config['app']['DEBUG']);

                self::$instance->addGlobal('configure',  self::$config);

                $app_js = DOC_ROOT.'public/app/js/'.$controller.'.js';
                if (file_exists($app_js)) {
                    self::$instance->addGlobal('app_js', "<script type='text/javascript' src='".URL.'app/js/'.$controller.".js'></script>");
                }

                $app_css = DOC_ROOT.'public/app/css/'.$controller.'.css';
                if (file_exists($app_css)) {
                    self::$instance->addGlobal('app_css', "<link rel='stylesheet' href='".URL.'app/css/'.$controller.".css' type='text/css'/>");
                }

                switch (Session::get('s_environment')) {
                    case 'dev':
                        $env = self::$config[Session::get('s_locale')]['app']['development'];
                    break;

                    case 'hom':
                        $env = self::$config[Session::get('s_locale')]['app']['statement'];
                    break;

                    case 'pro':
                        $env = self::$config[Session::get('s_locale')]['app']['production'];
                    break;
                }

                Session::set('s_token_csrf', md5(uniqid(rand(), true)));

                self::$instance->addGlobal('environment_current',  $env);

                self::$instance->addGlobal('server',         $_SERVER);
                self::$instance->addGlobal('session',        $_SESSION);
                self::$instance->addGlobal('post',           $_POST);
                self::$instance->addGlobal('get',            $_GET);
                self::$instance->addGlobal('request',        $_REQUEST);

                self::$instance->addGlobal('token_csrf',    Session::get('s_token_csrf'));

                self::$instance->addGlobal('flash',         Session::get('flash') ? Session::flash() : false);
                self::$instance->addGlobal('token',         Session::get('s_token'));

                self::$instance->addGlobal('controller',    $controller);
                self::$instance->addGlobal('method',        Router::getMethod());

                self::$instance->addGlobal('debugbar_header',   Debug::render()->renderHead());
                self::$instance->addGlobal('debugbar_body',     Debug::render()->render());
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return self::$instance;
    }

    public static function flash($message, $alert = 'info')
    {
        Session::set('flash', XHR::alertx($message, $alert));
    }

    public static function assign($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function render($template, $data = [])
    {
        ob_clean();
        $data = (self::$data) ? array_merge(self::$data, $data) : $data;

        $template = str_replace('.', '/', $template);

        echo self::getInstance()->render($template . EXT_TWIG, $data);
    }
}
