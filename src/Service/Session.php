<?php
/**
 * Classe controladora de sessões do PHP.
 *
 * @version 1.0
 */

namespace Service;

class Session
{
    /**
     * Variavel de instancia de toda vida da sessão.
     *
     * @var string
     */
    protected static $instance;

    /**
     * Variavel protegida carrega toda configuração da app.
     *
     * @var array
     */
    protected static $config;

    /**
     * Não é permitido fazer instancia do objeto.
     */
    protected function __construct() { }

    /**
     * Não é permitido fazer clones do objeto.
     */
    protected function __clone() { }

    /**
     * Não é permitido fazer serialização do objeto.
     */
    protected function __wakeup() { }

    /**
     * Método público para inicializar a classe de sessão.
     *
     * @method init()
     *
     * @param  none
     *
     * @return none
     */
    public static function getInstance()
    {
        if (self::$instance == false) {
            self::$config = autoload_config();

            ini_set('session.cookie_httponly', self::$config['app']['SESSION_COOKIE_HTTPONLY']);

            self::$instance = self::generateToken();

            @session_name(self::$instance);
            @session_cache_expire(self::$config['app']['SESSION_EXPIRE']);
            @session_start();

            self::set('s_token', self::$instance);
        }

        return self::$instance;
    }

    /**
     * Método público gerador de token.
     *
     * Retorna uma String criptografada em md5 contendo a chave de segurança da app,
     * o ip da maquina cliente e o navegador de acesso. Caso um deles seja alterado,
     * retornará um novo token.
     *
     * @method generateToken()
     *
     * @param none
     *
     * @return string
     */
    public static function generateToken()
    {
        return md5(self::$config['app']['KEY'] . REMOTE_ADDR . HTTP_USER_AGENT);
    }

    /**
     * Método público seta um valor em uma determinada sessão.
     *
     * @method set()
     *
     * @param string
     * @param string
     *
     * @return none
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Método público retorna um valor de uma determinada sessão.
     *
     * @method get()
     *
     * @param string
     *
     * @return string
     */
    public static function get($key)
    {
        return $_SESSION[$key] ? $_SESSION[$key] : false;
    }

    /**
     * Método público remove uma variável de uma determinada sessão.
     *
     * @method remove()
     *
     * @param string
     *
     * @return none
     */
    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Método público mostra o var_dump de toda sessão.
     *
     * @method dump()
     *
     * @param bool
     *
     * @return array
     */
    public static function dump($die = false)
    {
        dd($_SESSION, $die);
    }

    /**
     * Método público retorna valor de uma determinada sessão e se auto-destroi.
     *
     * @method flash()
     *
     * @param string
     *
     * @return string / Bool
     */
    public static function flash($key = false)
    {
        $value = self::get($key ? $key : 'flash');
        self::remove($key ? $key : 'flash');

        return $value ? $value : false;
    }

    /**
     * Método público retorna toda sessão em array.
     *
     * @method getArray()
     *
     * @param none
     * @retun Array
     */
    public static function getArray()
    {
        $data = [];

        foreach ($_SESSION as $key => $val) {
            $data[$key] = $val;
        }

        return $data;
    }

    /**
     * Método público destroi toda sessão.
     *
     * @method destroy()
     *
     * @param none
     *
     * @return none
     */
    public static function destroy()
    {
        @session_destroy();
    }
}
