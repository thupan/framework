<?php
/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/

 /**
  * Você pode criar funções dinamicas para as abas de detail. Todos os métodos
  * dinamicos fazem o intermédio com o javascript, portanto, esses métodos iniciam
  * com o nome 'xhr' no qual seu siguinificado é 'Xml Http Request' seguido do nome da ação
  * em camelcase.
  *
  * Exemplo para um combo dinamico:
  *
  * public function xhrComboLista() {
  *     Request::any($data);
  *     echo NomeDoModel::comboLista($data);
  * }
  *
  * para tabelas dinamicas:
  *
  * public function xhrPesquisarLista() {
  *     Request::any($data);
  *     $data = NomeDoModel::pesquisarLista($data);
  *     if($erro = NomeDoModel::getError()) {
  *         XHR::alert($erro, 'danger');
  *     }
  *
  *     // Para não mostrar determinados campos na tabela faça
  *     XHR::ignore(['CAMPO1','CAMPO2','CAMPON']);
  *     // Para ignorar um campo na saida use: XHR::ignore(['NOME_DO_CAMPO']);
  *      XHR::table('tabela', $data, ['detail','edit','delete'], ['CHAVE1','CHAVE2','CHAVEN']);
  * }
  *
  * para salvar (add/edit):
  *
  * public function xhrSaveLista() {
  *     Request::any($data);
  *
  *     if($data['edit']) {
  *         NomeDaModel::addLista($data);
  *         if($erro = NomeDaModel::getError()) {
  *             XHR::alert($erro, 'danger');
  *         }
  *     } else {
  *         NomeDaModel::editLista($data);
  *         if($erro = NomeDaModel::getError()) {
  *             XHR::alert($erro, 'danger');
  *         }
  *     }
  *     // impede novas requisições
  *     Request::filter(false);
  *     $this->xhrPesquisarLista();
  * }
  *
  * para deletar:
  *
  * public function xhrDeleteLista() {
  *   Request::any($data);
  *   NomeDaModel::deleteLista($data);
  *   if($erro = NomeDaModel::getError()) {
  *     XHR::alert($erro, 'danger');
  *   }
  *     // impede novas requisições
  *     Request::filter(false);
  *     $this->xhrPesquisarLista();
  * }
  */

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
     // para acessar um controller é obrigatorio estar logado.
     public $auth = true;

     /**
      * Guarda o nome deste controlador em variavél para uso posterior.
      *
      * @var $controller
      */
     public $controller = '{%controller_name%}';

     /**
      * Todas regras passadas neste metodo serão aplicadas no momento em que o
      * controlador for chamado.
      *
      * Padrão: seta o título de toda a aplicação com o nome do controlador.
      *
      * @return void
      */
     public function __construct() {
         View::$title = '{%controller_name%}';
     }

     /**
      * Quando o controlador for chamado sem informar um método para acesso
      * este método é carregado por padrão. (tela de pesquisa).
      *
      * @return void
      */
     public function index() {
         // Você pode alterar o título da página (opcional)
         //View::$title = '';

         // renderiza o template(twig) da tela principal(tela de pesquisa).
         View::render("$this->controller/index");
     }

     /**
      * Quando o controlador for chamado com o método detail este será responsável
      * em verirficar se o $id existe e renderizar a tela.
      *
      * @param  int  $id
      * @return void
      */
     public function detail($id) {
         // verifica se o id não existe
        if(!{%Controller%}::buscar($id)) {
            // Guarda em sessão temporária a mensagem de registro inválido
             View::flash("Você não pode editar o registro atual! <br/><br/> <p>Possíveis causas:</p><p>1 - O registro não existe mais.<br/>2 - O id passado é inválido.</p>", "warning");
             // Redireciona para página principal.
             Redirect::to("$this->controller");
         }
         // Se o $id existir, pega os dados do mesmo.
         $dados = {%Controller%}::buscar($id);
         // Prepara os dados para o template(twig).
         Request::prepare($dados);
         // Renderiza a tela de detalhes.
         View::render("$this->controller/detail");
     }

     /**
      * Quando o controlador for chamado com o método novo será carregado o template
      * de formulário para adição de dados ao banco.
      *
      * @return void
      */
     public function novo() {
         // verifica se algum campo obrigatorio não foi preenchido.
         Request::prepareRequired();
         // Caso o post falhe mantem o ultimo post ativo
         Request::prepareData();

         /*
          * Manipulação de dados, criação de combos aqui.
          */

          /**
           *  Exemplo de combo simples:
           *
           *  View::assign('nome_do_combo_para_o_template', Request::prepareCombo([
           *  1 => 'item 1',
           *  2 => 'item 2',
           *  3 => 'item 3'
           *  ]));
           *
           * Exemplo de combo com banco de dados:
           *
           * View::assign('nome_do_combo_para_o_template', NomeDaModel::comboNomeDoMetodo());
           */

           // renderiza o template(twig) da tela novo.
         View::render("$this->controller/novo");
     }

     /**
      * Quando o controlador for chamado com o método edit será carregado o template
      * de formulário para edição de dados ao banco.
      *
      * @param  int  $id
      * @return void
      */
     public function edit($id) {
         // verifica se o $id passado existe.
         if($data = {%Controller%}::buscar($id)) {
             // prepara a requisição para template
             Request::prepare($data, true);
             // prepara se houver campos obrigatórios
             Request::prepareRequired();

             /*
              * Manipulação de dados, criação de combos aqui.
              */

              /**
               *  Exemplo de combo simples:
               *
               *  View::assign('nome_do_combo_para_o_template', Request::prepareCombo([
               *  1 => 'item 1',
               *  2 => 'item 2',
               *  3 => 'item 3'
               *  ]));
               *
               * Exemplo de combo com banco de dados:
               *
               * View::assign('nome_do_combo_para_o_template', NomeDaModel::comboNomeDoMetodo());
               */

               // renderiza o template(twig) da tela de edição.
             View::render("$this->controller/edit");
         } else {
             // Guarda em sessão temporária a mensagem de registro inválido
             View::flash("Você não pode editar o registro atual! <br/><br/> <p>Possíveis causas:</p><p>1 - O registro não existe mais.<br/>2 - O id passado é inválido.</p>", "warning");
             // Redireciona para a página principal do controlador.
             Redirect::to("$this->controller");
         }
     }

     /**
      * Quando o controlador for chamado com o método delete o $id será removido do banco.
      *
      * @param  int  $id
      * @return void
      */
     public function delete($id) {
         // solicita a remoção no banco de dados.
         {%Controller%}::del($id);

         // se ocorrer um erro
         if($error = {%Controller%}::getError()) {
             // grava em sessão o erro.
             View::flash('Ocorreu um erro ao tentar remover o {%controller_name%}. <br/>' . $error, 'danger');
             // Redireciona para tela de detalhes do $id
             Redirect::to("$this->controller/detail/$id");
         }
         // Mostra mensagem de remoção se não ocorrer errors.
         View::flash("<b>$id</b> removido com sucesso!", 'success');
         // Redireciona
         Redirect::to($this->controller);
     }

     /**
      * Quando o controlador for chamado com o método save, este, será responsável
      * em verificar se é uma edição ou uma adição de dados e executará.
      *
      * @return void
      */
     public function save() {
         // determine os campos obrigatórios aqui.
         //Request::$require = [];

         // Pega a requisição da tela novo ou edit
         Request::post($data);

         // Verifica se os campos obrigatórios foram preenchidos.
         if($error = Request::getError()) {
             // Se houver erro, guarda mensagem de erro na sessão
             View::flash('Preencha os campos obrigatórios', 'danger');
             if($data['edit']) {
                 // se for modo edição, redireciona para a pagina.
                 Redirect::to("$this->controller/edit/" . $data['edit'] . Request::getRequired($error));
             } else {
                 // se for modo adição, redireciona para a pagina.
                 Redirect::to("$this->controller/novo/" . Request::getRequired($error), $data);
             }
         }

         // Se não houver erros, e for modo de edição.
         if($data['edit']) {

             // Solicita a edição e pega o $id
             $id = {%Controller%}::edit($data);

             // Se ocorrer erro.
             if($erro = {%Controller%}::getError()) {
                 // Guarda na sessão o erro
                 View::flash($erro, 'danger');
                 // Redireciona para a página de edição do $id
                 Redirect::to("$this->controller/edit/" . $data['edit']);
             } else {
                 // Se não ocorrer erro, guada mensagem de ok na sessão.
                 View::flash("Registro <b>" . $id . "</b> alterado com sucesso!", 'success');
                 // Redireciona para pagina de detalhes do $id
                 Redirect::to("$this->controller/detail/$id");
             }

         } else {
             // Se for para adicionar um dado, solicita e pega o id|fgpk
             $id = {%Controller%}::add($data);

             // Se ocorrer um erro
             if($erro = {%Controller%}::getError()) {
                 // Guarda o erro na sessão
                 View::flash($erro, 'danger');
                 // Redireciona para pagina de novo
                 Redirect::to("$this->controller/novo/", $data);
             } else {
                 // Se tudo estiver ok, guarda a mensagem na sessao
                 View::flash("Registro <b>" . $id . "</b> adicionado com sucesso!", 'success');
                 // Redireciona para a tela de detail
                 Redirect::to("$this->controller/detail/$id");
             }
         }
     }

     public function xhrImprimirPesquisa() {
         // verifica as requisições feitas ou não.
         Request::any($data);
         // Solicita a pesquisa com as requisições passadas ou não.
         $data = {%Controller%}::pesquisar($data);

         // Se ocorrer um erro.
         if($erro = {%Controller%}::getError()) {
             // Guarda a mensagem de erro
             View::flash($erro, 'danger');
             // Redireciona para pagina principal do controlador.
             Redirect::to("$this->controller");
         }

         // Sem erros, então carrega o PDF da pesquisa.
         Pdf{%Controller%}Pesquisa::conteudo($data);
     }

     /**
      * Este método carrega a tabela de pesquisa dinamicamente.
      *
      * @return void
      */
     public function xhrPesquisar() {
         // Faz as requisições
         Request::any($data);

         // Solicita a pesquisa com as requisições
         $data = {%Controller%}::pesquisar($data);

         // Se ocorrer um erro
         if($erro = {%Controller%}::getError()) {
             // faz um alerta com a msg de erro
             XHR::alert($erro, 'danger');
         }

         // XHR::ignore(['CHAVE_PARA_N_MOSTRAR_NA_TABELA']);
         XHR::table('tabela', $data, ['detail'], {%arrayPkTable%});
     }
 }
