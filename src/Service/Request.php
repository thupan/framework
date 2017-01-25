<?php
/**
 * Classe controladora de requisições do PHP.
 *
 * @version 1.0
 */

namespace Service;

/*
 * Esta classe utiliza o serviço de sessão
 */
use Kernel\View;
use Service\Session;
use Service\Validator;

class Request
{
    public static $require = [];

    protected static $error = [];

    /**
     * Método de request para $_POST.
     *
     * @method post()
     *
     * @param array
     * @param bool
     *
     * @return bool
     */
    public static function post(&$data = [], $upper = true)
    {
        return self::getData($_POST, $data, $upper);
    }

    /**
     * Método de request para $_GET.
     *
     * @method get()
     *
     * @param array
     * @param bool
     *
     * @return bool
     */
    public static function get(&$data = [], $upper = true)
    {
        return self::getData($_GET, $data, $upper);
    }

    /**
     * Método de request para $_REQUEST.
     *
     * @method any()
     *
     * @param array
     * @param bool
     *
     * @return bool
     */
    public static function any(&$data = [], $upper = true)
    {
        return self::getData($_REQUEST, $data, $upper);
    }

    public static function getError()
    {
        return self::$error;
    }

    /**
     * Método protegido que faz a validação das requisições.
     *
     * @method getData()
     *
     * @param Request
     * @param array
     * @param bool
     *
     * @return bool
     */
    protected static function getData($request, &$data = [], $upper = true)
    {
        if (!empty($request)) {
            foreach ($request as $key => $value) {
                if (empty($key)) {
                    continue;
                }

                if (self::$require) {
                    if (in_array($key, self::$require)) {
                        if (Validator::blank($value)) {
                            self::$error[] = $key;
                        }
                    }
                }

                $data[str_replace('|','.',$key)] = ($upper) ? mb_strtoupper(addslashes(trim($value)), 'UTF-8') : addslashes(trim($value));
            }

            if ($request['request_data']) {
                parse_str_to_array($request['request_data'], $data_rd);

                foreach ($data_rd as $key => $value) {
                    if (empty($key)) {
                        continue;
                    }

                    $data_rd[$key] = ($upper) ? mb_strtoupper(addslashes(trim($value)), 'UTF-8') : addslashes(trim($value));
                }

                $data = array_merge($data, $data_rd);

                unset($data['request_data']);
            }
        } else {
            // nenhuma requisição passada sem nenhum retorno de dados.
            return false;
        }

        // todo request foi verificado e teve retorno de dados.
        return true;
    }

    public static function prepareRequired()
    {
        self::get($required);
        View::assign('required', explode(',', $required['required']));
    }

    public static function getRequired($error)
    {
        foreach ($error as $key) {
            $str .= "$key,";
        }

        $str = rtrim($str, ',');

        return '?required='.$str;
    }

    public static function prepare(&$data, $oneRow = false)
    {
        foreach ($data as $key => $array) {
            foreach ($array as $key => $value) {
                $a[$key] = $value;
                View::assign($key, $value);
            }
        }

        return ($oneRow) ? $data = $a : false;
    }

    public static function prepareData() {
        self::get($data, false);

        if($data['twigformfields']) {
            $data = json_decode(base64_decode($data['twigformfields']));

            foreach($data as $key => $value) {
                View::assign($key, $value);
            }
        }
    }

    public static function prepareCombo($array)
    {
        foreach ($array as $key => $value) {
            $a[] = ['ID' => $key, 'TEXT' => $value];
        }

        return $a;
    }

    // impede que um filtro de sql seja montando se for false
    public static function filter($status)
    {
        $_REQUEST['filter'] = (!$status) ? true : false;
    }

    // limpa toda requisicao passada
    public static function clean($request = null)
    {
        switch ($request) {
            case 'post':
                unset($_POST);
            break;

            case 'get':
                unset($_GET);
            break;

            case 'any':
                unset($_REQUEST);
            break;

            default:
                unset($_POST);
                unset($_REQUEST);
                unset($_GET);
        }
    }

    public static function phpInput(&$data)
    {
            $data = json_decode(file_get_contents('php://input'),true);
    }
}
