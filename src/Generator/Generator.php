<?php

namespace Generator;

class Generator {
  public    $program_name,
            $query,
            $queryBuscar,
            $search,
            $table,
            $tablePk,
            $tablePk_var,
            $tableHeader,
            $HTMLNew,
            $HTMLNewFields,
            $HTMLEdit,
            $HTMLEditFields,
            $HTMLEditFieldsID,
            $HTMLDetail,
            $HTMLDetailBtn,
            $ColumnsDB;

  protected $pathController,
            $pathModel,
            $pathViewCss,
            $pathViewJs,
            $pathView,
            $pathTemplate;

  public function __construct() {
    //print_r($_POST);
    $this->setPaths();

    $this->program_name     = $_POST['TX_URL'];
    $this->tablePk          = $this->cleanPk($_POST['TABLE_PK']);
    $this->tablePk_var      = $this->cleanPk_var($_POST['TABLE_PK']);
    $this->table            = $_POST['TABLE_NAME'];
    $this->query            = $_POST['TABLE_SQL'];
    $this->queryBuscar      = $_POST['TABLE_SQL_BUSCAR'];
    $this->ColumnsDB        = $this->Options($_POST);
    $this->search           = $this->setSearch($_POST);
    $this->tableHeader      = $this->setHeader($_POST);
    $this->HTMLNew          = $this->setNewPage($_POST);
    $this->HTMLNewFields    = $this->setNewSql($_POST);
    $this->HTMLEdit         = $this->setEditPage($_POST);
    $this->HTMLEditFields   = $this->setEditSql($_POST);
    $this->HTMLEditFieldsID = $this->setEditIds($_POST);
    $this->HTMLDetail       = $this->setDetailPage($_POST);
    $this->HTMLDetailBtn    = $this->setDetailButtons($_POST);

    if(!$this->createFiles()) {
      echo '*** ocorreu um erro ao tentar gerar os arquivos. verifique se todos os templates existem.';
    }
  }
  private function setPaths() {
    $this->pathController = DOC_ROOT . 'app/Http/Controllers/';
    $this->pathModel      = DOC_ROOT . 'app/Http/Models/';
    $this->pathView       = DOC_ROOT . 'app/Http/Views/';

    $this->pathViewCss    = DOC_ROOT . 'public/app/css/';
    $this->pathViewJs     = DOC_ROOT . 'public/app/js/';

    $this->pathTemplate   = __DIR__  . DS . '../Support/Templates/Generator/tpl/';
  }

  private function cleanPk($pk) {
      $pk = explode('-', $pk);

      foreach($pk as $k => $v) {
        $clean = explode('.', $v);
        $new  .= $clean[1].'-';
      }

      return rtrim($new, '-');
  }

  private function cleanPk_var($pk) {
      $pk = explode('-', $pk);

      foreach($pk as $k => $v) {
        $clean = explode('.', $v);
        $new  .= '$'.$clean[1].'-';
      }

      return rtrim($new, '-');
  }

  public function Options($post) {
      $campos = $post['campos'];
      foreach($campos as $key => $val) {
          if(!$val) continue;
          $val = str_replace(':', '.', $val);
          $options .= " <option value='$val'>".explode('_', explode('.', $val)[1])[1]."</option> ";
      }

      return $options;
  }

  public function query() {
    $campos  = $_POST['campos'];
    $tabelas = $_POST['TABLE_NAME'];

    $all = (is_null($campos)) ? " <b style='color:red'>*</b> " : false;
    $query = "<b style='color:#F56A6A'>SELECT</b>$all<br/> ";
    foreach($campos as $key => $val) {
        if(!$val) continue;

        $options .= " <option value='$val'>".explode('.', $val)[1]."</option> ";

      $val = str_replace(':', '.', $val);

      $text = explode('.', $val);

      if(substr($text[1],0,3) == 'DT_') {
          $query .= 'TO_CHAR('.$val.', \'dd/mm/yyyy\') '.$text[1].',<br/>';
      } else {
          $query .= $val.',<br/>';
      }
    }

    $query = rtrim($query, ',<br/>');

    $query .= " <br/><br/><b style='color:#F56A6A'>FROM</b><br/> ";

    foreach($tabelas as $key => $val) {
      $query .= $val.' '.$val.',<br/>';
    }

    $query = rtrim($query, ',<br/>');

    $this->ColumnsDB = $options;
    return $query;
  }

  private function setSearch($post) {
    $rel = $post['TABLE_PK'];
    $rel = explode('-', $rel);

    if($rel) {
      if(count($rel) > 1) {
        $query = ' AND ';
      } else {
        $query = ' WHERE ';
      }

      $campos = $post['campos'];

      foreach($campos as $key => $val) {
        $val = str_replace(':','.', $val);
        $query .= $val.' LIKE \'%$columns%\' OR ';
      }
      $query = rtrim($query, ' OR ');
    }

    return $query;
  }
  private function setHeader($post) {
    $campos = $post['campos'];

    if($campos) {
      $table = "<th>#</th>\n";
      foreach($campos as $key => $val) {
        $val    = explode(':', $val);
        $val    = explode('_', $val[1]);
        $table .= "\t\t\t\t\t<th>{$val[1]}</th>\n";
      }

      $table .= "\t\t\t\t\t<th class='text-center'>AÇÕES</th>";
    }

    return $table;
  }

  private function getFieldType($type, $label) {

    $html = "\n<div class='col-md-12'>\n";

    switch($type) {
      case 1:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 2:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <textarea class='form-control' id='{$label}' name='{$label}' required>{\$dados[0].{$label}}</textarea>
          </div>

          ";
      break;

      case 3:
        $html .= "

          <div class='form-group'>
            <input type='hidden' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' />
          </div>

          ";
      break;

      case 4:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control' type='password' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 5:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <select class='form-control' name='{$label}' id='{$label}' required>
                <option value=''>Escolha...</option>
            </select>
          </div>

          ";
      break;

      case 6:
        $html .= "

          <div class='form-group'>
            <input type='file' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' />
          </div>

          ";
      break;

      case 7:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cpf' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 8:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cnpj' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 9:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control data' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 10:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <div class='input-group'>
              <div class='input-group-addon'>R$</div>
              <input class='form-control moeda' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
            </div>
          </div>

          ";
      break;

      case 11:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control telefone' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 12:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control celular' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;

      case 13:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <div class='input-group'>
              <div class='input-group-addon'>@</div>
              <input class='form-control email' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
            </div>
          </div>

          ";
      break;

      case 14:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <div class='input-group'>
              <div class='input-group-addon'>@</div>
              <input class='form-control email-gov' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
            </div>
          </div>

          ";
      break;

      case 15:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cep' type='text' name='{$label}' id='{$label}' value='{\$dados[0].{$label}}' required />
          </div>

          ";
      break;
    }

    $html .= "\n</div>\n";

    return $html;
  }

  private function tableView($fields) {
      $form = "<table class='table table-striped'>";
      foreach($fields as $key => $val) {
          $val = explode(':', $val);

          $form .= "<tr>
                        <td><b>{$val[1]}</b></td>
                        <td>
                            {\$dados[0].$val[1]}
                            <input id='{$val[1]}' name='{$val[1]}' value='{\$dados[0].$val[1]}' type='hidden' />
                        </td>
                    </tr>";
      }
      $form .= "</table>";

      return $form;
  }

  private function setNewPage($post) {
    $campos = $post['campos_novo'];

    foreach($campos as $key => $val) {
      //$form .= $this->getFieldType($post[$val], explode(':', $val)[1]);
      $form .= $this->getFieldType($val, explode(':', $key)[1]);

      //echo $key.'->'.$val.'<br/>';
    }

    return $form;
  }

  private function setNewSql($post) {
      $campos = $post['campos_novo'];

      //print_r($campos);

        $data = "'". $this->tablePk ."' => \$fgpk,\n\t\t\t  ";

      foreach($campos as $key => $val) {
        $data .= "'".explode(':', $key)[1]."' => \$data['".explode(':', $key)[1]."'],\n\t\t\t";
      }

      return $data;
  }

  private function setEditSql($post) {
      $campos = $post['campos_edit'];

      //print_r($campos);

      foreach($campos as $key => $val) {
        $data .= "'".explode(':', $val)[1]."' => \$data['".explode(':', $val)[1]."'],\n\t\t\t";
      }

      return $data;
  }

  private function setEditIds($post) {
      $id = $post['TABLE_PK'];

      $ids = explode('-', $id);

      foreach($ids as $k => $v) {
          $query .= " $v = '\$".  explode('.', $v)[1]   ."' AND";
      }

      $query = rtrim($query, ' AND');

      return $query;
  }

  private function setEditPage($post) {
      $campos   = $post['campos_novo'];
      $campos_e = $post['campos_edit'];

      $nao_editar = array_diff(array_keys($campos), array_values($campos_e));

      $form = $this->tableView($nao_editar);

      foreach($campos_e as $key => $val) {
          foreach($campos as $k => $v) {
              if($val == $k) {
                  //$form .= $this->getFieldType($post[$val], explode(':', $val)[1]);
                  $form .= $this->getFieldType($v, explode(':', $k)[1]);
              }
          }
      }
      return $form ;
  }

  private function setDetailPage($post) {
      $campos   = $post['campos_detail'];
      $form = $this->tableView($campos);
      return $form ;
  }

  private function setDetailButtons($post) {
      $botoes = $post['botoes_acao'];

      $botao = "";

      foreach($botoes as $key => $val) {
          switch($val) {

              case 'remover':
              $botao .= '<a href="{$url}'.strtolower($this->program_name).'/delete/{$dados[0].'.$this->tablePk.'}" class="btn btn-danger delete">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            Remover
                        </a> &nbsp;';
              break;

              case 'editar':
              $botao .= '<a href="{$url}'.strtolower($this->program_name).'/edit/{$dados[0].'.$this->tablePk.'}" class="btn btn-warning">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            Editar
                        </a>&nbsp;';
              break;
          }
      }

      return $botao;
  }

  private function decodeTemplate($file) {
    // controller
    $file = str_replace('{%Controller%}',           ucwords(strtolower($this->program_name)), $file);
    $file = str_replace('{%controller_name%}',      strtolower($this->program_name), $file);
    $file = str_replace('{%tablePk%}',              $this->tablePk,     $file);
    $file = str_replace('{%tablePk_var%}',          $this->tablePk_var,     $file);
    $file = str_replace('{%TABLE_NAME%}',           strtoupper($this->table[0]), $file);
    $file = str_replace('{%ColumnsDB%}',            $this->ColumnsDB, $file);
    // model
    $file = str_replace('{%QueryPesquisar%}',       $this->query,       $file);
    $file = str_replace('{%QueryCamposPesquisa%}',  $this->search,      $file);
    $file = str_replace('{%QueryBuscar%}',          $this->queryBuscar, $file);
    // index.tpl
    $file = str_replace('{%tableHeader%}',          $this->tableHeader, $file);
    // novo.tpl
    $file = str_replace('{%HTMLNew%}',              $this->HTMLNew, $file);
    $file = str_replace('{%HTMLNewFields%}',        $this->HTMLNewFields, $file);
    $file = str_replace('{%HTMLNew-valid%}',        'validar', $file);
    // editar.tpl
    $file = str_replace('{%HTMLEdit%}',             $this->HTMLEdit, $file);
    $file = str_replace('{%HTMLEditFields%}',       $this->HTMLEditFields, $file);
    $file = str_replace('{%HTMLEditFieldsID%}',     $this->HTMLEditFieldsID, $file);
    $file = str_replace('{%HTMLEdit-valid%}',       'validar', $file);
    // detail.tpl
    $file = str_replace('{%HTMLDetail%}',           $this->HTMLDetail, $file);
    $file = str_replace('{%HTMLDetailBtn%}',        $this->HTMLDetailBtn, $file);


    $file = str_replace('{%GC_VERSION%}',         '1.0', $file);
    $file = str_replace('{%GC_DATE%}',            date('d/m/Y G:i:s'), $file);
    $file = str_replace('{%GC_DEVELOPER%}',       Session::get('TX_LOGIN'), $file);
    $file = str_replace('{%GC_MACHINE%}',         gethostname(), $file);

    return $file;
  }

  private function createFolder($mode=0777) {
    // se não tem programa, não tem nada!
    if(!$this->program_name) return false;
    // cria o diretorio passado.
    mkdir($this->pathView . $this->program_name, $mode, true);
    // tudo ok!
    return true;
  }

  private function createFiles() {
    // só permite executar este método se tiver um nome de programa.
    if(!$this->program_name) return false;

    // verifica todos os templates
    foreach(glob(__DIR__ . DS . '../Support/Templates/Generator/tpl/*.tpl') as $localfile) {
        // somente se existir o template
        if( file_exists($localfile) ) {

          // carrega na memoria
          $fp      = fopen($localfile, 'r') or die('Não foi possível abrir o arquivo: '.$localfile);
          $file    = fread($fp, filesize($localfile));
          fclose($fp);

          // decodifica o arquivo
          $file = $this->decodeTemplate($file);

          // pega o nome do arquivo atual
          $filename = explode('/', $localfile);
          $filename = end($filename);
          $filename = explode('.', $filename);

          // faz o devido tratamento de path e nome do programa referente o seu tipo
          switch($filename[0]) {
            case 'controller':
              $path    = $this->pathController;
              $program = strtolower($this->program_name) . EXT_PHP;
            break;

            case 'model':
              $path    = $this->pathModel;
              $program = ucfirst(strtolower($this->program_name)) . EXT_PHP;
            break;

            case 'css':
              $path    = $this->pathViewCss;
              $program = strtolower($this->program_name).'.css';
            break;

            case 'js':
              $path = $this->pathViewJs;
              $program = strtolower($this->program_name).'.js';
            break;

            case 'twig':

            // cria os diretorios
            $this->createFolder();

            switch($filename[1]) {
              case 'index':
                $path = $this->pathView . strtolower($this->program_name) . DS;
                $program = 'index' . EXT_TWIG;
              break;
              case 'detail':
                $path = $this->pathView . strtolower($this->program_name) . DS;
                $program = 'detail' . EXT_TWIG;
              break;
              case 'editar':
                $path = $this->pathView . strtolower($this->program_name) . DS;
                $program = 'edit' . EXT_TWIG;
              break;
              case 'novo':
                $path = $this->pathView . strtolower($this->program_name) . DS;
                $program = 'novo' . EXT_TWIG;
              break;
            }

            break;
          }
        } else {
          // arquivo não existe! mata tudo!
          return false;
        }

        // cria os arquivos
        $fp  = fopen($path . $program, 'w+') or die('Não foi possível criar o arquivo: ' . $program);
        fwrite($fp, $file);
        fclose($fp);
    }

    return true;
  }
}
