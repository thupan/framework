<?php

use \Service\Session;

if (!function_exists('mask')){
    function  mask($mask,$str){
      $str = preg_replace("/[^0-9]/", "", $str);
      for($i=0;$i<strlen($str);$i++){
         $mask[strpos($mask,"#")] = $str[$i];
      }
      return $mask;
    }        
  }

if(!function_exists('dateDiff')) {
	function dateDiff($date_start, $date_end)
	{
		$date_start = implode('-', array_reverse(explode('/', substr($date_start, 0, 10)))) . substr($date_start, 10);
		$date_end   = implode('-', array_reverse(explode('/', substr($date_end, 0, 10)))) . substr($date_end, 10);

		$dts = new \DateTime($date_start);
		$dte = new \DateTime($date_end);

		$interval = $dts->diff($dte);

		return $interval;
	}
}

if(!function_exists('dateConvert')) {
	function dateConvert($date, $type)
	{
		switch ($type) {
			case 'pt-br':
			case 'pt-BR':
				$date = implode('/', array_reverse(explode('-', substr($date, 0, 10)))) . substr($date, 10);
				break;
			case 'us':
			case 'US':
				$date = implode('-', array_reverse(explode('/', substr($date, 0, 10)))) . substr($date, 10);
				break;
		}

		return $date;
	}
}

if(!function_exists('moneyConvert')) {
	function moneyConvert($money, $type)
	{
		switch ($type) {
			case 'us':
			case 'US':
				$money = str_replace('.', '', $money);
				$money = str_replace(',', '.', $money);
				$money = number_format($money, 2, '.', '');
				break;
			case 'pt-br':
			case 'pt-BR':
				$money = number_format($money, 2, ',', '.');
				break;
		}

		return $money;
	}
}

/**
 * Função global para fazer concat em scripts.
 *
 * @method load_script()
 * @param String
 * @param String
 * @return none
 */
 if(!function_exists('load_script')) {
  function load_script($type, $file) {
      $news_lock = [];
      $news_file = [];
      $lock_file = [];

      $plugins_file = __DIR__ . '/../../../../../' . 'public/plugins.'.$type;
      $plugins_lock = __DIR__ . '/../../../../../' . 'public/plugins.'.$type.'.lock';

      if(file_exists($plugins_lock)) {
          $lock_file = file_get_contents($plugins_lock);
          $plugins_lock_array = explode("\n", $lock_file);

          foreach($file as $row) {
              if(!in_array($row, $plugins_lock_array)) {
                  $news_lock[] = $row;
                  $news_file[] = file_get_contents($row);
              }
          }
      }

      if(file_exists($plugins_file)) {
          foreach($news_file as $n) {
              $type != 'js' ? $n = preg_replace('/\s/',' ',$n) : false;
              file_put_contents($plugins_file, $n . "\n\n", FILE_APPEND);
          }

          foreach($news_lock as $nl) {
              file_put_contents($plugins_lock, $nl . "\n", FILE_APPEND);
          }
      } else {
          foreach($file as $f) {
              $current = file_get_contents($f);
              $type != 'js' ? $current = preg_replace('/\s/',' ',$current) : false;
              file_put_contents($plugins_file, $current . "\n\n", FILE_APPEND);
              file_put_contents($plugins_lock, $f . "\n", FILE_APPEND);
          }
      }
  }
}

/**
 * Função global para retornar um timestamp para uma data.
 *
 * @method get_timestamp()
 * @param  Date
 * @return timestamp
 */
if(!function_exists('get_timestamp')) {
    function get_timestamp($date) {

        if(!$date) return 0;

        $array = explode('/', $date);
        return mktime(0, 0, 0, $array[1], $array[0], $array[2]);
    }
}

/**
 * Função global para carregar todas as rotas criadas.
 *
 * @method autoload_routes()
 * @param  none
 * @return none
 */
if(!function_exists('autoload_routes')) {
    function autoload_routes() {
        foreach(glob(__DIR__ . '/../../../../../' . 'app/Routes/route.*.php') as $file) {
            file_exists($file) ? require $file : false;
        }
    }
}

/**
 * Função global para remover pontos e virgulas.
 *
 * @method remove_dots()
 * @param  String
 * @return String
 */
if(!function_exists('remove_dots')) {
    function remove_dots($string, $dot) {
        if(is_array($dot)) {
            foreach($dot as $index) {
                $string = str_replace($index, '', $string);
            }
        } else {
            $string = str_replace($dot, '', $string);
        }

        return $string;
    }
}

/**
 * Função global para carregar todas os filtros criados.
 *
 * @method autoload_filters()
 * @param  none
 * @return none
 */
if(!function_exists('autoload_filters')) {
    function autoload_filters() {
        foreach(glob(__DIR__ . '/../../../../../' . 'app/Filters/filter.*.php') as $file) {
            file_exists($file) ? require $file : false;
        }
    }
}

/**
 * Função global para carregar todas as configurações da app.
 * @method autoload_config()
 * @param  none
 * @return Array
 */
if(!function_exists('autoload_config')) {
    function autoload_config() {
        foreach(glob(__DIR__ . '/../../../../../' . 'app/Config/*.php') as $file) {
            $key = explode('/', $file);
            $key = end($key);
            $key = strtolower(str_replace('.php', '', $key));
            $array[$key] = require $file;
        }

        foreach(glob(__DIR__ . '/../../../../../' . 'app/Language/*/*') as $file) {
            $keys = explode('/', $file);
            $lang = $keys[ sizeof($keys) - 2 ];
            $key  = $keys[ sizeof($keys) - 1 ];
            $key  = strtolower(str_replace('.php', '', $key));

            $array[$lang][$key] = require $file;
        }

        return $array;
    }
}

/**
 * Função global para retornar a traduacao.
 * @method translate(param, param)
 * @param  String Arquivo de idiomas
 * @param  String Chave do arquivo de idiomas
 * @param  Array  Troca de palavras sequencias
 * @return Array
 */
if(!function_exists('translate')) {
    function translate($l = 'app', $k = null, $a = []) {

        foreach(glob(__DIR__ . '/../../../../../' . 'app/Language/*/*') as $file) {
            $keys = explode('/', $file);
            $lang = $keys[ sizeof($keys) - 2 ];
            $key  = $keys[ sizeof($keys) - 1 ];
            $key  = strtolower(str_replace('.php', '', $key));

            $array[$lang][$key] = require $file;
        }

        $key = $array[\Service\Session::get('s_locale')][$l][$k];

        if($a) {
            foreach($a as $index => $value) {
                    $key = str_replace('{'.$index.'}', $value, $key);
            }
        }

        return $key;
    }
}

/**
 * Função global de carregar maquinas de acesso ao ambiente
 *
 * Quando utilizado a maquina que estiver na lista de ambientes so podera ter acesso
 * ao ambiente em que ela esta!
 *
 * @method autoload_machines()
 * @param none
 * @return none
 */
 if(!function_exists('autoload_machines')) {
    function autoload_machines() {
        foreach( autoload_config()['machines'] as $env => $array ) {
            foreach($array as $machine) {
                if( in_array(gethostname(), autoload_config()['machines'][$env]) ) {
                        Session::set('s_environment', $env);
                        return $env != 'dev' ? true : false;
                }
            }
        }

        return false;
    }
}

/**
 * Função global de debug
 *
 * Quando utilizado mostra um var_dump ou print_r do campo passado, podendo finalizar
 * o tempo de vida da aplicação. default: false.
 *
 * @method dd()
 * @param String
 * @param Bool
 * @param Bool
 * @return none
 */
if(!function_exists('dd')) {
    function dd($variable, $die = false, $dump = true, $color = 'red') {
        ob_start();
        echo "<pre style='color:$color'>", ($dump) ? var_dump($variable) : print_r($variable) ,"</pre>";
        ob_end_flush();
        $die ? die('--- FIM DA APLICAÇÃO ---') : false;
    }
}

/**
 * Função global de carregar um arquivo válido
 *
 * Quando utilizado carrega um arquivo.
 *
 * @method load_file()
 * @param String
 * @return File
 */
if(!function_exists('load_file')) {
    function load_file($file) {
        $file = __DIR__ . '/../../../../../' . $file;
        if(file_exists($file)) {
            return require $file;
        }
        return false;
    }
}

/**
 * Função global de conversão de Urlencode para Array
 *
 * Quando utilizado converte uma string urlencode para array.
 *
 * @method parse_str_to_array()
 * @param String
 * @param Array
 * @return none
 */
if(!function_exists('parse_str_to_array')) {
    function parse_str_to_array($string, &$array) {
        $string = explode('&', $string);
        foreach($string as $key => $value) {
            if(!$value) continue;
            $val = explode('=', $value);
            $val[0] = str_replace('+', ' ', rawurldecode($val[0]));
            $val[1] = str_replace('+', ' ', rawurldecode($val[1]));
            $array[$val[0]] = $val[1];
        }
    }
}

/**
 * Função global de split no camelcase
 *
 * Quando utilizado faz um split em uma string camelcase.
 *
 * @method load_file()
 * @param String
 * @return String
 */
if(!function_exists('explodeCamelCase')) {
    function explodeCamelCase($str) {
      return preg_split('/(?<=\\w)(?=[A-Z])/', $str);
    }
}
