<?php

namespace Routing;

use \Service\Session;
use \Service\Redirect;
use \Service\Response;
use \Service\XHR;

class Router
{
    protected static $app;

    public static $fallback = true;
    public static $halts = true;

    const BEFORE = 'before';
    const AFTER = 'after';
    const DEFAULT_METHOD = 'index';

    protected static $filter = false;
    protected static $filters = [];

    protected static $uri;
    protected static $controller;
    protected static $method;
    protected static $args = [];

    public static $routes = array();
    public static $methods = array();
    public static $callbacks = array();
    public static $errorCallback;
    public static $patterns = array(
       ':any' => '[^/]+',
       ':num' => '-?[0-9]+',
       ':all' => '.*',
       ':hex' => '[[:xdigit:]]+',
       ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}',
   );

    public static function getControllerName($lower = true)
    {
        $controller = explode('\\', self::getController());
        $controller = end($controller);

        if ($lower) {
            $controller = strtolower(str_replace('Controller', '', $controller));
        }

        return $controller;
    }

    public static function getController()
    {
        return self::$controller;
    }

    public static function setController($newController)
    {
        self::$controller = $newController;
    }

    public static function getMethod()
    {
        return self::$method;
    }

    public static function getArgs()
    {
        return self::$args;
    }

    public static function getUri()
    {
        return self::$uri;
    }

    public static function getFilters()
    {
        return self::$filters;
    }

    public static function __callstatic($method, $params)
    {
        $uri = '/'.$params[0];
        $callback = $params[1];

        array_push(self::$routes, $uri);
        array_push(self::$methods, strtoupper($method));
        array_push(self::$callbacks, $callback);
    }

    public static function error($callback)
    {
        self::$errorCallback = $callback;
    }

    public static function haltOnMatch($flag = true)
    {
        self::$halts = $flag;
    }

    public static function invokeObject($callback, $matched = null, $msg = null)
    {
        $last = explode('/', $callback);
        $last = end($last);

        $segments = explode('@', $last);

        $controller = $segments[0];
        $method = $segments[1];

        $controller = "\\App\\Http\\Controllers\\$controller";
        $controller = new $controller($msg);

        call_user_func_array(array($controller, $method), $matched ? $matched : array());
    }

    public static function uri_prepare()
    {
        $dir = explode('/', PHP_SELF);

        $key = array_search('index.php', $dir);

        for ($i = 0; $i <= $key; ++$i) {
            $droot[] = $dir[$i];
            unset($dir[$i]);
        }

        $dir = array_values($dir);
        $dir = implode('/', $dir);

        $url = rtrim($dir, '/');

        define('ROUTER_REQUEST', $url);

        $droot[array_search('index.php', $droot)] = '';

        $Path = str_replace('//','/', implode('/', $droot));

        self::$uri = $url;

        return $Path;
    }

    public static function filter($action, $name, $handle)
    {
        self::$filter = true;
        self::$filters[$action][$name] = $handle;
    }

    public static function parseFilters($filters)
    {
        $beforeFilter = [];
        $afterFilter = [];

        if (isset($filters[self::BEFORE])) {
            $beforeFilter = array_intersect_key(self::$filters, array_flip((array) $filters[self::BEFORE]));
        }

        if (isset($filters[self::AFTER])) {
            $afterFilter = array_intersect_key(self::$filters, array_flip((array) $filters[self::AFTER]));
        }

        return array($beforeFilter, $afterFilter);
    }

    public static function autoDispatch()
    {
        self::uri_prepare();

        if (strpos(self::$uri, '/') === 0) {
            self::$uri = substr(self::$uri, strlen('/'));
        }

        self::$uri = trim(self::$uri, '/');
        self::$uri = ($amp = strpos(self::$uri, '&')) !== false ? substr(self::$uri, 0, $amp) : self::$uri;

        $parts = explode('/', self::$uri);

        self::$controller = array_shift($parts);
        self::$method = array_shift($parts);

        self::$controller = self::$controller ? self::$controller : DEFAULT_CONTROLLER;
        self::$method = self::$method ? self::$method : self::DEFAULT_METHOD;
        self::$args = !empty($parts) ? $parts : array();

        $ControllerClass = ucfirst(self::$controller).'Controller';

        if (!file_exists(DOC_ROOT . "app/Http/Controllers/$ControllerClass.php")) {
            return false;
        }

        self::$controller = "\\App\\Http\\Controllers\\$ControllerClass";
        $controller = new self::$controller();

        // verifica os filtros
        if (self::$filter) {
            foreach (self::$filters as $action => $name) {
                foreach ($name as $callback) {
                    if (self::BEFORE === $action) {
                        $callback();
                    }
                }
            }

            if (method_exists($controller, self::$method)) {
                call_user_func_array(array($controller, self::$method), self::$args);

                return true;
            } else {
                $method = explodeCamelCase(self::$method);

                if ($method[0] !== 'xhr') {
                    call_user_func_array(array($controller, self::DEFAULT_METHOD), self::$args);
                } else {
                    echo '<tr><td colspan="30">'.XHR::alert('Oopss, ocorreu um erro ao carregar o método <b>'.self::$method.'</b>. verifique sua chamada javascript ou se o método xhr existe no seu respectivo controlador.', 'danger').'</td></tr>';
                }
            }
        } else {
            if (method_exists($controller, self::$method)) {
                call_user_func_array(array($controller, self::$method), self::$args);

                return true;
            } else {
                $method = explodeCamelCase(self::$method);

                if ($method[0] !== 'xhr') {
                    call_user_func_array(array($controller, self::DEFAULT_METHOD), self::$args);
                } else {
                    echo '<tr><td colspan="30">'.XHR::alert('Oopss, ocorreu um erro ao carregar o método <b>'.self::$method.'</b>. verifique sua chamada javascript ou se o método xhr existe no seu respectivo controlador.', 'danger').'</td></tr>';
                }
            }
        }

        return false;
    }

    public static function dispatch()
    {
        // Se tiver filtros, carrega todos aqui.
        autoload_filters();

        // Se tiver, rotas carrega todos aqui.
        autoload_routes();

        // Verifica e prepara a URL
        self::uri_prepare();

        self::$method = $_SERVER['REQUEST_METHOD'];

        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);

        self::$routes = str_replace('//', '', self::$routes);

        $found_route = false;

        $query = '';
        $q_arr = array();
        if (strpos(self::$uri, '&') > 0) {
            $query = substr(self::$uri, strpos(self::$uri, '&') + 1);
            self::$uri = substr(self::$uri, 0, strpos(self::$uri, '&'));
            $q_arr = explode('&', $query);
            foreach ($q_arr as $q) {
                $qobj = explode('=', $q);
                $q_arr[] = array($qobj[0] => $qobj[1]);
                if (!isset($_GET[$qobj[0]])) {
                    $_GET[$qobj[0]] = $qobj[1];
                }
            }
        }

        if (in_array(self::$uri, self::$routes)) {
            $route_pos = array_keys(self::$routes, self::$uri);
            foreach ($route_pos as $route) {
                if (self::$methods[$route] == self::$method || self::$methods[$route] == 'ANY') {
                    $found_route = true;

                    if (!is_object(self::$callbacks[$route])) {
                        self::invokeObject(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    } else {
                        call_user_func(self::$callbacks[$route]);
                        if (self::$halts) {
                            return;
                        }
                    }
                }
            }
        } else {
            $pos = 0;

            foreach (self::$routes as $route) {
                $route = str_replace('//', '', $route);

                if (strpos($route, ':') !== false) {
                    $route = str_replace($searches, $replaces, $route);
                }

                if (preg_match('#^'.$route.'$#', self::$uri, $matched)) {
                    if (self::$methods[$pos] == self::$method || self::$methods[$pos] == 'ANY') {
                        $found_route = true;

                        array_shift($matched);

                        if (!is_object(self::$callbacks[$pos])) {
                            self::invokeObject(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        } else {
                            call_user_func_array(self::$callbacks[$pos], $matched);
                            if (self::$halts) {
                                return;
                            }
                        }
                    }
                }
                ++$pos;
            }
        }

        if (self::$fallback) {
            $found_route = self::autoDispatch();
        }

        if (!$found_route) {
            if (!self::$errorCallback) {
                self::$errorCallback = function () {
                    $method = explodeCamelCase(self::$method);

                    if ($method[0] != 'xhr') {
                        if(Session::get('s_loggedIn')) {
                            header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
                            Response::error('404');
                        } else {
                            Redirect::to("/");
                        }
                    }
               };
            }

            if (!is_object(self::$errorCallback)) {
                self::invokeObject(self::$errorCallback, null, 'No routes found.');
                if (self::$halts) {
                    return;
                }
            } else {
                call_user_func(self::$errorCallback);
                if (self::$halts) {
                    return;
                }
            }
        }

        // verifica os filtros
        if (self::$filter) {
            foreach (self::$filters as $action => $name) {
                foreach ($name as $callback) {
                    if (self::AFTER === $action) {
                        $callback();
                    }
                }
            }
        }
    }
}
