<?php
/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/

 namespace App\Http\Models;

 use \App\Domain\AppModel as Model;

 class {%Controller%} extends Model {
     // faz a validação do model automatica.
     public $validate = true;

     /**
      * Retorna os dados de todos os registros cadastrados.
      *
      * @param Array
      * @return Array
      */
     public static function pesquisar($data) {
         // para alternar banco (quando a app utiliza multi-conexoes)
         // self::$connection = 'conn2';

         // se houver requisição
         if(!is_null($data)) {
             // transforma todos em variaveis php
             extract($data);
             // prepara os campos para injeção na query de pesquisa.
             $columns = self::getColumns($data);
         }
           // retorno da query
           return self::query("{%QueryPesquisar%}");
     }

     /**
      * Retorna dados de um determinado registro.
      *
      * @param Int
      * @return Array
      */
     public static function buscar($id) {
         return self::query("{%QueryBuscar%}");
     }

     /**
      * Adiciona os campos passados em uma tabela.
      *
      * @param Array
      * @return Int
      */
     public static function add($data) {
         // extrai os campos passando em variaveis
        extract($data);
        // pega a fgpk
        $fgpk = self::fgpk('{%TABLE_NAME%}');

        // prepara os dados
        $data = [
            {%HTMLNewFields%}
         ];

         // faz o insert na tabela
        $return = self::insert("{%TABLE_NAME%}", $data);

        // retorna a fgpk se tudo deu certo se não recebe false.
        return ($return) ? $fgpk : false;
     }

     /**
      * Atualiza um registro.
      *
      * @param Array
      * @return Int
      */
     public static function edit($data) {
       // extrai os campos passando em variaveis
       extract($data);

       // prepara os campos para o update
       $data = [
            {%HTMLEditFields%}
         ];

       // executa o update
       $return = self::update("{%TABLE_NAME%}", $data, " {%HTMLEditFieldsID%} ");

       // se tudo estiver ok, retorna a chave da tabela se nao false.
       return ($return) ? {%tablePk_var%} : false;
     }

     /**
      * Remove um registro do banco.
      *
      * @param Int
      * @return Array
      */
     public static function del($id) {
       // se o registro existir, remove da tabela.
       return self::delete("{%TABLE_NAME%}", "{%tablePk%} = '{$id}'");
     }
 }
