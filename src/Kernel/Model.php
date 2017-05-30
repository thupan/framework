<?php

namespace Kernel;

use \Database\Database;

class Model
{
    public static $connection = false;
    public static $all        = false;
    public static $persistence = true;

    protected static $db = null;

    // validacao do model automatico.
    public $validate = true;

    public function validateAccess()
    {
        return $this->validate;
    }

    public static function dbConnection()
    {
        $config = autoload_config();

        if (!self::$db) {
            self::$db = new Database();
        }

        !self::$connection ? self::$connection = $config['database']['DB_DEFAULT_CONN'] : false;

        return self::$db;
    }

    public static function getColumns($data, $where = true, $ignore = [])
    {
        // ser verdadeiro, forca a perda de filtros
        if ($data['filter']) {
            unset($data['filter']);
            return false;
        } else {
            unset($data['filter']);
        }

        // remove campos do filtro
        if($ignore) {
          foreach($data as $key => $value) {
            foreach($ignore as $k) {
              if($key === $k) {
                unset($data[$key]);
              }
            }
          }
        }

        foreach ($data as $key => $value) {

            if(is_null($value)) {
                unset($data[$key]);
            }

            if($data['TX_PESQUISA']) {
                $value_all = $data['TX_PESQUISA'];
                unset($data['TX_PESQUISA']);
                continue;
            }

            // se nao tiver nenhum valor passa pro proximo campo
            if (!$value_all && !$value) {
                continue;
            }

            // ignora paginacao
            if ($key == 'p') {
                continue;
            }

            $k = explode(':', $key);

            // prepara as regras de pesquisa por campo
            switch ($k[1]) {
                case 'ANY':
                    $value = '%'.$value.'%';
                break;

                case 'FIRST':
                    $value = '%'.$value;
                break;

                case 'LAST':
                    $value = $value.'%';
                break;

                case 'THIS':
                    $value = $value;
                break;

                default:
                    $value = $value.'%';
            }

            if($where === false) {
            // pesquisa por todos os campos
            if($value_all) {
                $mob .= " UPPER($k[0]) LIKE UPPER('%$value_all%') OR";
                continue;
            } else {
                // pesquisa por campos combinados
                $opt = 'AND';
            }
        } else {
            if($value_all) {
                $mob2 .= " UPPER($k[0]) LIKE UPPER('%$value_all%') OR";
                continue;
            }
            $value = (!$value_all) ? $value : '%'. $value_all .'%';
            $opt = ' WHERE ';
            $where = false;
        }

            // monta a query
            $columns[] = " $opt (UPPER($k[0]) LIKE UPPER('$value')) ";
        }

        if($mob) {
            return ' AND (' . rtrim($mob, ' OR') .')';
        } else if($mob2) {
            return ' WHERE (' . rtrim($mob2, ' OR') .')';
        } else {
            return ($columns) ? implode(' ', $columns) : false;
        }
    }

    public static function getColumnsKey($keys = [], $value, $type = 'AND')
    {
        if (self::$all) {
            return false;
        }

        foreach ($keys as $index => $key) {
            $k = explode(':', $key);

            switch ($k[1]) {
                case 'ANY':
                    $val = '%'.$value.'%';
                break;

                case 'FIRST':
                    $val = '%'.$value;
                break;

                case 'LAST':
                    $val = $value.'%';
                break;

                case 'THIS':
                    $val = $value;
                break;

                default:
                    $val = $value.'%';
            }

            ($key && $value) ? $search[] = " $type (UPPER($k[0]) LIKE '$val') " : false;

            unset($val);
        }

        return ($search) ? implode(' ', $search) : false;
    }










    public static function ociChangePassword($username, $old_password, $new_password) {
        return self::dbConnection()->getInstance(self::$connection)->ociChangePassword($username, $old_password, $new_password);
    }

    public static function getQueries($connection = null) {
        return self::dbConnection()->getInstance($connection ? $connection : self::$connection)->getQueries();
    }

    public static function getError()
    {
        return implode('<br/>', self::dbConnection()->getInstance(self::$connection)->getError());
    }

    public static function query($sql, $type = null)
    {
        $data = self::dbConnection()->getInstance(self::$connection)->query($sql, $type);

        if(self::$persistence) {
            self::execute("COMMIT");
        }

        return $data;
    }

    public static function find($table, $where = null)
    {
        $data = self::dbConnection()->getInstance(self::$connection)->find($table, $where);

        if(self::$persistence) {
            self::execute("COMMIT");
        }

        return $data;
    }

    public static function insert($table, $data)
    {
        $data = self::dbConnection()->getInstance(self::$connection)->insert($table, $data);

        if(self::$persistence) {
            self::execute("COMMIT");
        }

        return $data;
    }

    public static function update($table, $data, $where)
    {
        $data = self::dbConnection()->getInstance(self::$connection)->update($table, $data, $where);

        if(self::$persistence) {
            self::execute("COMMIT");
        }

        return $data;
    }

    public static function delete($table, $where)
    {
        $data = self::dbConnection()->getInstance(self::$connection)->delete($table, $where);

        if(self::$persistence) {
            self::execute("COMMIT");
        }

        return $data;
    }

    public static function execute($sql)
    {
        return self::dbConnection()->getInstance(self::$connection)->execute($sql);
    }

    public static function callProcedure($sp_name = null, $sp_args = []) {
        return self::dbConnection()->getInstance(self::$connection)->callProcedure($sp_name, $sp_args);
    }
}
