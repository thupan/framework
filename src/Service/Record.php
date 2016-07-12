<?php

/**
 * Classe de mipulação de registros PDO.
 *
 * @package \Service\Record
 * @version 1.0
 *
 */

namespace Service;

class Record {
    /**
     * Método público retorna um JOIN de PDO por um ID.
     *
     * @method merge()
     * @param Aarray
     * @param Aarray
     * @param Aarray
     * @return Array
     */
    public static function merge($table1, $table2, $pk = [])
    {
        foreach($table1 as $key => $field) {
            foreach($table2 as $k => $f) {
                if($field[$pk[0]] == $f[$pk[1]]) {
                    $array[] = array_merge($field, $f);
                }
            }
        }
        return $array;
    }

    /**
     * Método público retorna um sort de um array definido.
     * Organizar as colunas na visualização do grid.
     *
     * @method sortCol()
     * @param Aarray
     * @param Aarray
     * @return Array
     */
    public static function sortCol($result,$array)
    {
        foreach ($result as $key => $fields) {
         $dados[] = self::sortkeys($fields,$array);
        }
        return $dados;
    }
    /**
     * Método privado retorna um sort de um array definido.
     *
     * @method sortKeys()
     * @param Aarray
     * @param Aarray
     * @return Array
     */
    private static function sortKeys($array, $order)
    {
        uksort($array, function ($a, $b) use ($order) {
            $pos_a = array_search($a, $order);
            $pos_b = array_search($b, $order);

            if ($pos_a === false)
                return 1;
            if ($pos_b === false)
                return -1;

            return $pos_a - $pos_b;
        });

        return $array;
    }
    /**
     * Método público retorna uma orderna um array em PDO.
     *
     * @method sortRecord()
     * @param Aarray
     * @param String
     * @param boolean
     * @return Array
     */
    public static function sortRecord($records, $field, $reverse=false)
    {
        $hash = array();

        foreach($records as $key => $record)
        {
            $hash[$record[$field].$key] = $record;
        }

        ($reverse)? krsort($hash) : ksort($hash);

        $records = array();

        foreach($hash as $record)
        {
            $records []= $record;
        }

        return $records;
    }
     /**
     * Método público retorna uma pesquisa em um PDO.
     *
     * @method findRecord()
     * @param Aarray
     * @param String
     * @param String
     * @return Array
     */
    public static function findRecord($dados,$coluna,$valor)
    {
        $cont = 0;

        foreach ($dados as $key => $value) {

              if(strpos($value[$coluna],$valor)!== false){
                $obj[$cont] = $value;
                $cont++;
              }else if(strpos($value[$coluna],strtoupper($valor))!== false){
                $obj[$cont] = $value;
                $cont++;
              } else if(strpos($value[$coluna],ucwords(strtolower($valor)))!== false){
                $obj[$cont] = $value;
                $cont++;
              }else if(strpos($value[$coluna],strtolower($valor))!== false){
                $obj[$cont] = $value;
                $cont++;
              }else if(strpos($value[$coluna],ucfirst(strtolower($valor)))!== false){
                $obj[$cont] = $value;
                $cont++;
              }
        }

        return $obj;
    }

}
