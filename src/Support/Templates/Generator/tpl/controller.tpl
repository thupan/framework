<?php
/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * CRIADOR POR: {%GC_DEVELOPER%}@{%GC_MACHINE%}
 ************************************************************/

 namespace App\Http\Controllers;

 use \App\Domain\AppController as Controller;
 use \App\Domain\AppView as View;
 use \App\Domain\AppModel as Mode;

 use \App\Exception\Exception as Exception;

 use \App\Helpers\XHR;
 use \App\Helpers\Request;
 use \App\Helpers\Redirect;
 use \App\Helpers\Session;

 use \App\Http\Models\{%Controller%};
 use \App\Pdf\Pdf{%Controller%}Pesquisa;

 class {%Controller%}Controller extends Controller implements \App\Http\Controllers\Interfaces\CRUD {

     public $controller = '{%controller_name%}';

     public function index() {
         //View::$title = '';
         View::render("$this->controller/index");
     }

     public function detail($id) {
         if(!{%Controller%}::buscar($id)) {
             View::flash("Você não pode editar o registro atual! <br/><br/> <p>Possíveis causas:</p><p>1 - O registro não existe mais.<br/>2 - O id passado é inválido.</p>", "warning");
             Redirect::to("$this->controller");
         }

         $dados = {%Controller%}::buscar($id);
         Request::prepare($dados);
         View::render("$this->controller/detail");
     }

     public function novo() {
         Request::prepareRequired();

         /*
          * Codificar aqui.
          */

         View::render("$this->controller/novo");
     }

     public function edit($id) {
         if($data = {%Controller%}::buscar($id)) {

             Request::prepare($data, true);
             Request::prepareRequired();

             /*
              * Codificar aqui.
              */

             View::render("$this->controller/edit");
         } else {
             View::flash("Você não pode editar o registro atual! <br/><br/> <p>Possíveis causas:</p><p>1 - O registro não existe mais.<br/>2 - O id passado é inválido.</p>", "warning");
             Redirect::to("$this->controller");
         }
     }

     public function delete($id) {
         {%Controller%}::del($id);

         if($error = {%Controller%}::getError()) {
             View::flash('Ocorreu um erro ao tentar remover o {%controller_name%}. <br/>' . $error, 'danger');
             Redirect::to("$this->controller/detail/$id");
         }

         View::flash("<b>$id</b> removido com sucesso!", 'success');
         Redirect::to($this->controller);
     }

     public function save() {

         // campos obrigatórios.
         //Request::$require = [];

         Request::post($data);

         if($error = Request::getError()) {
             View::flash('Preencha os campos obrigatórios', 'danger');
             if($data['edit']) {
                 Redirect::to("$this->controller/edit/" . $data['edit'] . Request::getRequired($error));
             } else {
                 Redirect::to("$this->controller/novo/" . Request::getRequired($error));
             }
         }

         if($data['edit']) {

             $id = {%Controller%}::edit($data);

             if({%Controller%}::getError()) {
                 View::flash($erro, 'danger');
                 Redirect::to("$this->controller/edit/" . $data['edit']);
             } else {
                 View::flash("<b>" . $id . "</b> alterado com sucesso!", 'success');
                 Redirect::to("$this->controller/detail/$id");
             }

         } else {
             $id = {%Controller%}::add($data);

             if($erro = {%Controller%}::getError()) {
                 View::flash($erro, 'danger');
                 Redirect::to("$this->controller/novo/");
             } else {
                 View::flash("<b>" . $id . "</b> adicionado com sucesso!", 'success');
                 Redirect::to("$this->controller/detail/$id");
             }
         }
     }

     public function xhrImprimirPesquisa() {
         Request::any($data);

         $data = {%Controller%}::pesquisar($data);

         if({%Controller%}::getError()) {
             View::alert({%Controller%}::getError(), 'danger');
             Redirect::to("$this->controller");
         }

         Pdf{%Controller%}Pesquisa::conteudo($data);
     }

     public function xhrImprimirGrupo() {
         Request::any($data);

         $data = {%Controller%}::pesquisarGrupo($data);

         if({%Controller%}::getError()) {
             View::alert({%Controller%}::getError(), 'danger');
             Redirect::to("$this->controller/detail/" . $data['{%tablePk%}']);
         }

         PdfUsuarioDetailGrupo::conteudo($data);
     }

     public function xhrPesquisar() {
         Request::any($data);

         if({%Controller%}::getError()) {
             View::alert({%Controller%}::getError(), 'danger');
         }

         $data = {%Controller%}::pesquisar($data);

         XHR::table($data, "$this->controller:{%tablePk%}");
     }
 }
