<?php
/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * CRIADOR POR: {%GC_DEVELOPER%}@{%GC_MACHINE%}
 ************************************************************/

 namespace App\Http\Models;

 use \App\Domain\AppModel as Model;

 class {%Controller%} extends Model {

     /*=============================================>>>>>
     = Tela principal =
     ===============================================>>>>>*/

     /**
      * Retorna os dados de todos os registros cadastrados.
      *
      * @param Array
      * @return Array
      */
     public static function pesquisar($data) {
         // para alternar banco
         // self::$connection = 'conn2';
         if(!is_null($data)) {
             extract($data);
             $columns = self::getColumns($data);
         }

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
     /*= End of Tela principal =*/
     /*=============================================<<<<<*/


     /*=============================================>>>>>
     = CRUD MVC =
     ===============================================>>>>>*/

     public static function add($data) {
       extract($data);

       $fgpk = self::fgpk('{%TABLE_NAME%}');

       $data = [
            {%HTMLNewFields%}
         ];

       $return = self::insert("{%TABLE_NAME%}", $data);

       return ($return) ? $fgpk : false;
     }

     public static function edit($data) {
       extract($data);

       $data = [
            {%HTMLEditFields%}
         ];

       $return = self::update("{%TABLE_NAME%}", $data, " {%HTMLEditFieldsID%} ");

       return ($return) ? {%tablePk_var%} : false;
     }

     public static function del($id) {
       return self::delete("{%TABLE_NAME%}", "{%tablePk%} = '{$id}'");
     }

     /*= End of CRUD MVC =*/
     /*=============================================<<<<<*/
 }
