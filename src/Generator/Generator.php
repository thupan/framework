<?php

namespace Generator;

use Service\Session;

class Generator
{
    public $program_name,
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
            $ColumnsDB,
            $HeaderClean,
            $FieldsPDF,
            $colHgridView;
            

    protected $pathController,
            $pathModel,
            $pathViewCss,
            $pathViewJs,
            $pathView,
            $pathTemplate;

    public function __construct()
    {
        //dd($_REQUEST);

        $this->program_name = strtolower($_REQUEST['TX_URL']);
        $this->tablePk = $this->cleanPk($_REQUEST['TABLE_PK']);
        $this->tablePk_var = $this->cleanPk_var($_REQUEST['TABLE_PK']);
        $this->table = $_REQUEST['TABLE_NAME'];
        $this->query = $_REQUEST['TABLE_SQL'];
        $this->queryBuscar = $_REQUEST['TABLE_SQL_BUSCAR'];

        $this->setPaths();

        $this->ColumnsDB = $this->Options();
        $this->search = $this->setSearch();
        $this->tableHeader = $this->setHeader();
        $this->HTMLNew = $this->setNewPage();
        $this->HTMLNewFields = $this->setNewSql();
        $this->HTMLEdit = $this->setEditPage();
        $this->HTMLEditFields = $this->setEditSql();
        $this->HTMLEditFieldsID = $this->setEditIds();
        $this->HTMLDetail = $this->setDetailPage();
        $this->HTMLDetailBtn = $this->setDetailButtons();
        $this->colHgridView = $this->setColHgridView();
    }
    public function generate()
    {
        // cria os diretorios
      $this->createFolder();

        if (!$this->createFiles()) {
            echo '*** ocorreu um erro ao tentar gerar os arquivos. verifique se todos os templates existem.';
        }
    }

    private function setPaths()
    {
        $this->pathController = DOC_ROOT . 'app/Http/Controllers/';
        $this->pathModel      = DOC_ROOT . 'app/Http/Models/';
        $this->pathView       = DOC_ROOT . 'app/Http/Views/';

        $this->pathViewCss    = DOC_ROOT . 'app/Http/Views/' . $this->program_name . '/css/';
        $this->pathViewJs     = DOC_ROOT . 'app/Http/Views/' . $this->program_name . '/js/';
        $this->pathPdf       = DOC_ROOT  . 'app/Pdf/';

        $this->pathTemplate = __DIR__ . DS . '../Support/Templates/Generator/tpl/';
    }

    private function cleanPk($pk)
    {
        $pk = explode('-', $pk);

        foreach ($pk as $k => $v) {
            $clean = explode('.', $v);
            $new  .= $clean[1].'-';
        }

        return rtrim($new, '-');
    }

    private function cleanPk_var($pk)
    {
        $pk = explode('-', $pk);

        foreach ($pk as $k => $v) {
            $clean = explode('.', $v);
            $new  .= '$'.$clean[1].'-';
        }

        return rtrim($new, '-');
    }

    public function setColHgridView()
    {
        $campos = $_REQUEST['campos'];
        foreach ($campos as $key => $val) {
            if (!$val) {
                continue;
            }
            $val = str_replace(':', '.', $val);
            $col .= "'".explode('.', $val)[1]."',";
        }

        return $col;

    }

    public function Options()
    {
        $campos = $_REQUEST['campos'];
        foreach ($campos as $key => $val) {
            if (!$val) {
                continue;
            }
            $val = str_replace(':', '.', $val);
            $options .= " <option value='$val'>".explode('_', explode('.', $val)[1])[1].'</option> ';
        }

        return $options;
    }

    public function query()
    {
        $campos = $_REQUEST['campos'];
        $tabelas = $_REQUEST['TABLE_NAME'];

        if (!$campos) {
            return false;
        }
        if (!$tabelas) {
            return false;
        }
    //dd($_REQUEST,1);

    $all = (is_null($campos)) ? " <b style='color:red'>*</b> " : false;
        $query = "<b style='color:#F56A6A'>SELECT</b>$all<br/> ";
        foreach ($campos as $key => $val) {
            if (!$val) {
                continue;
            }

            $options .= " <option value='$val'>".explode('.', $val)[1].'</option> ';

            $val = str_replace(':', '.', $val);

            $text = explode('.', $val);

            if (substr($text[1], 0, 3) == 'DT_') {
                $query .= 'TO_CHAR('.$val.', \'dd/mm/yyyy\') '.$text[1].',<br/>';
            } else {
                $query .= $val.',<br/>';
            }
        }

        $query = rtrim($query, ',<br/>');

        $query .= " <br/><br/><b style='color:#F56A6A'>FROM</b><br/> ";

        foreach ($tabelas as $key => $val) {
            $query .= $val.' '.$val.',<br/>';
        }

        $query = rtrim($query, ',<br/>');

        $this->ColumnsDB = $options;

        return $query;
    }

    private function setSearch()
    {
        $rel = $_REQUEST['TABLE_PK'];

        if (!$rel) {
            return false;
        }
        $rel = explode('-', $rel);

        if ($rel) {
            if (count($rel) > 1) {
                $query = ' AND ';
            } else {
                $query = ' WHERE ';
            }

            $campos = $_REQUEST['campos'];

            foreach ($campos as $key => $val) {
                $val = str_replace(':', '.', $val);
                $query .= $val.' LIKE \'%$columns%\' OR ';
            }
            $query = rtrim($query, ' OR ');
        }

        return $query;
    }
/*

<tr>
  <th>#</th>
  <th>LOGIN</th>
  <th>FUNCIONÁRIO</th>
  <th>UNIDADE GESTORA</th>
  <th>MOTIVO</th>
  <th>AÇÕES</th>
</tr>
<tr class="search-fields">
  <form class="form-fields">
    <td><input name="USUARIO.ID_USUARIO" class="form-control" type='text'/></td>
    <td><input name="USUARIO.TX_LOGIN" class="form-control" type='text'/></td>
    <td><input name="PESSOA.TX_NOME:ANY" class="form-control" type='text'/></td>
    <td><input name="UNID_GESTORA_PESSOA.TX_UNIDADE_GESTORA:ANY" class="form-control" type='text'/></td>
    <td><input name="USUARIO.TX_MOTIVO_SITUACAO:ANY" class="form-control" type='text'/></td>
    <td style='width:130px !important;'>
      <button type="button" class="btn btn-primary search">
        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
      </button>

      <button type="button" class="btn btn-default search-refresh">
        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
      </button>
    </td>
</form>
</tr>

*/
    private function setHeader()
    {
        $campos = $_REQUEST['campos'];

        if ($campos) {
            foreach ($campos as $index => $key) {
                $key2   = explode(':', $key)[1];
                $label  = explode('_', $key2);
                $label  = (strtolower($label[0]) == 'id') ? '#' : $label[1];
                $key    = str_replace(':', '.', $key);
                $html[] = "\t'$label:$key:ANY'";
            }
            return implode(",\n", $html);
        }
        return false;
    }

    // private function setHeader_old()
    // {
    //     $campos = $_REQUEST['campos'];
    //
    //     if (!$campos) {
    //         return false;
    //     }
    //     if ($campos) {
    //         $i = 0;
    //         foreach ($campos as $key => $val) {
    //             $val = explode(':', $val);
    //             $val = explode('_', $val[1]);
    //             if($val[0] === 'ID' && $i === 0) {
    //                 $val[1] = '#';
    //                 $i++;
    //             }
    //             $pdf_header .= " '$val[1]',\n ";
    //             $table .= "<th>{$val[1]}</th>\n";
    //         }
    //
    //         $table .= "<th class='text-center'>AÇÕES</th>\n";
    //
    //         $table .= "<tr class='search-fields'>\n
    //                         <form class='form-fields'>\n";
    //
    //         foreach ($campos as $key => $val) {
    //                 $val = str_replace(':', '.', $val);
    //                 $field_name = explode('.', $val);
    //                 if(count($field_name) > 1) {
    //                     $pdf_fields .= "\$linha['{$field_name[1]}'],\n";
    //                 } else {
    //                     $pdf_fields .= "\$linha['{$field_name[0]}'],\n";
    //                 }
    //
    //                 $table .= "<td><input name='$val:ANY' class='form-control Enter' type='text'/></td>\n";
    //         }
    //
    //         $table .= "
    //         <td style='width:130px !important;'>
    //           <button type='button' class='btn btn-primary search'>
    //             <span class='glyphicon glyphicon-search' aria-hidden='true'></span>
    //           </button>\n\n
    //
    //           <button type='button' class='btn btn-default search-refresh'>
    //             <span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>
    //           </button>\n\n
    //         </td> \n\n";
    //
    //         $table .= "</form>\n
    //                     </tr>\n";
    //     }
    //
    //     $this->HeaderClean = rtrim($pdf_header, ',');
    //     $this->FieldsPDF   = rtrim($pdf_fields, ',');
    //     return $table;
    // }

    private function getFieldType($type = false, $label = false)
    {
        if (!$type) {
            return false;
        }
        $html = "\n<div class='col-md-12'>\n";

        switch ($type) {
      case 1:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 2:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <textarea class='form-control' id='{$label}' name='{$label}' required>{{{$label}}}</textarea>
          </div>

          ";
      break;

      case 3:
        $html .= "

          <div class='form-group'>
            <input type='hidden' name='{$label}' id='{$label}' value='{{{$label}}}' />
          </div>

          ";
      break;

      case 4:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control' type='password' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 5:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <select class='form-control form-control-select2' name='{$label}' id='{$label}' required>

            </select>
          </div>

          ";
      break;

      case 6:
        $html .= "

          <div class='form-group'>
            <input type='file' name='{$label}' id='{$label}' value='{{{$label}}}' />
          </div>

          ";
      break;

      case 7:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cpf' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 8:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cnpj' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 9:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control data' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 10:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <div class='input-group'>
              <div class='input-group-addon'>R$</div>
              <input class='form-control moeda' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
            </div>
          </div>

          ";
      break;

      case 11:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control telefone' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 12:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control celular' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;

      case 13:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <div class='input-group'>
              <div class='input-group-addon'>@</div>
              <input class='form-control email' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
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
              <input class='form-control email-gov' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
            </div>
          </div>

          ";
      break;

      case 15:
        $html .= "

          <div class='form-group'>
            <label class='form-control-label obrigatorio' for='{$label}'>{$label}</label>
            <input class='form-control cep' type='text' name='{$label}' id='{$label}' value='{{{$label}}}' required />
          </div>

          ";
      break;
    }

        $html .= "\n</div>\n";

        return $html;
    }

    private function tableView($fields)
    {
        if (!$fields) {
            return false;
        }

        $form = '<table class="table table-striped table-hover table-detail">';
        foreach ($fields as $key => $val) {
            $val = explode(':', $val);

            $form .= "<tr>
                        <td>
                            <label>{$val[1]}</label>
                            <span>{{{$val[1]}}}</span>
                            <input id='{$val[1]}' name='{$val[1]}' value='{{{$val[1]}}}' type='hidden' />
                        </td>
                    </tr>";
        }
        $form .= '</table>';

        return $form;
    }

    private function setNewPage()
    {
        $campos = $_REQUEST['campos_novo'];

        if (!$campos) {
            return false;
        }

        foreach ($campos as $key => $val) {
            //$form .= $this->getFieldType($_REQUEST[$val], explode(':', $val)[1]);
      $form .= $this->getFieldType($val, explode(':', $key)[1]);

      //echo $key.'->'.$val.'<br/>';
        }

        return $form;
    }

    private function setNewSql()
    {
        $campos = $_REQUEST['campos_novo'];
        if (!$campos) {
            return false;
        }
      //print_r($campos);

        $data = "'".$this->tablePk."' => \$fgpk,\n\t\t\t  ";

        foreach ($campos as $key => $val) {
            $data .= "'".explode(':', $key)[1]."' => \$data['".explode(':', $key)[1]."'],\n\t\t\t";
        }

        return $data;
    }

    private function setEditSql()
    {
        $campos = $_REQUEST['campos_edit'];
        if (!$campos) {
            return false;
        }
      //print_r($campos);

      foreach ($campos as $key => $val) {
          $data .= "'".explode(':', $val)[1]."' => \$data['".explode(':', $val)[1]."'],\n\t\t\t";
      }

        return $data;
    }

    private function setEditIds()
    {
        $id = $_REQUEST['TABLE_PK'];

        if (!$id) {
            return false;
        }
        $ids = explode('-', $id);

        foreach ($ids as $k => $v) {
            $query .= " $v = '\$".explode('.', $v)[1]."' AND";
        }

        $query = rtrim($query, ' AND');

        return $query;
    }

    private function setEditPage()
    {
        $campos = $_REQUEST['campos_novo'];
        $campos_e = $_REQUEST['campos_edit'];

        if (!$campos || !$campos_e) {
            return false;
        }
        $nao_editar = array_diff(array_keys($campos), array_values($campos_e));

        $form = $this->tableView(array_unique($nao_editar));

        foreach ($campos_e as $key => $val) {
            foreach ($campos as $k => $v) {
                if ($val == $k) {
                    //$form .= $this->getFieldType($_REQUEST[$val], explode(':', $val)[1]);
                  $form .= $this->getFieldType($v, explode(':', $k)[1]);
                }
            }
        }

        return $form;
    }

    private function setDetailPage()
    {
        $campos = $_REQUEST['campos_detail'];
        if (!$campos) {
            return false;
        }

        $form = $this->tableView(array_unique($campos));

        return $form;
    }

    private function setDetailButtons()
    {
        $botoes = $_REQUEST['botoes_acao'];

        if (!$botoes) {
            return false;
        }
        $botao = '';

        foreach ($botoes as $key => $val) {
            switch ($val) {

              case 'remover':
              $botao .= '<a href="{{URL}}'.strtolower($this->program_name).'/delete/{{'.$this->tablePk.'}}" class="btn btn-danger delete">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            {{langApp.delete}}
                        </a> &nbsp;';
              break;

              case 'editar':
              $botao .= '<a href="{{URL}}'.strtolower($this->program_name).'/edit/{{'.$this->tablePk.'}}" class="btn btn-warning">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            {{langApp.edit}}
                        </a>&nbsp;';
              break;
          }
        }

        return $botao;
    }

    private function arrayPkTable() {
        $array = explode('-', $this->tablePk);

        return  "['". implode("','", $array) . "']";
    }

    private function decodeTemplate($file)
    {
        $file = str_replace('{%arrayPkTable%}', $this->arrayPkTable(), $file);
        // controller
        $file = str_replace('{%Controller%}',           ucwords(strtolower($this->program_name)), $file);
        $file = str_replace('{%controller_name%}',      strtolower($this->program_name), $file);
        $file = str_replace('{%tablePk%}',              $this->tablePk,     $file);
        $file = str_replace('{%tablePk_var%}',          $this->tablePk_var,     $file);
        $file = str_replace('{%TABLE_NAME%}',           strtoupper($this->table[0]), $file);
        $file = str_replace('{%ColumnsDB%}',            $this->ColumnsDB, $file);
        $file = str_replace('{%colHgridView%}',         $this->colHgridView, $file);

    // model
        $this->query = str_replace(',', ",\n", $this->query);
        $this->query = str_replace("\n\n\n\n", "\n", $this->query);


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

        //pdf
        $file = str_replace('{%HeaderClean%}',        $this->HeaderClean, $file);
        $file = str_replace('{%FieldsPDF%}',        $this->FieldsPDF, $file);

        //comentario
        $file = str_replace('{%GC_VERSION%}',         '1.0', $file);
        $file = str_replace('{%GC_DATE%}',            date('d/m/Y G:i:s'), $file);
        $file = str_replace('{%GC_DEVELOPER%}',       Session::get('TX_LOGIN'), $file);
        $file = str_replace('{%GC_MACHINE%}',         gethostname(), $file);


        return $file;
    }

    private function createFolder($mode = 0777)
    {
        // se não tem programa, não tem nada!
    if (!$this->program_name) {
        return false;
    }
    // cria o diretorio passado.
    if (!file_exists($this->pathView.$this->program_name)) {
        mkdir($this->pathView.$this->program_name, $mode, true);
    }

    if (!file_exists($this->pathViewCss)) {
        mkdir($this->pathViewCss, $mode, true);
    }

    if (!file_exists($this->pathViewJs)) {
        mkdir($this->pathViewJs, $mode, true);
    }
    // tudo ok!
    //return true;
    }

    private function createFiles()
    {
        // só permite executar este método se tiver um nome de programa.
    if (!$this->program_name) {
        return false;
    }

    // verifica todos os templates
    foreach (glob(__DIR__.DS.'../Support/Templates/Generator/tpl/*.tpl') as $localfile) {
        // somente se existir o template
        if (file_exists($localfile)) {

          // carrega na memoria
          $fp = fopen($localfile, 'r') or die('Não foi possível abrir o arquivo: '.$localfile);
            $file = fread($fp, filesize($localfile));
            fclose($fp);

          // decodifica o arquivo
          $file = $this->decodeTemplate($file);

          // pega o nome do arquivo atual
          $filename = explode('/', $localfile);
            $filename = end($filename);
            $filename = explode('.', $filename);

          // faz o devido tratamento de path e nome do programa referente o seu tipo
          switch ($filename[0]) {
            case 'controller':
              $path = $this->pathController;
              $program = ucfirst(strtolower($this->program_name)).'Controller'.EXT_PHP;
            break;

            case 'model':
              $path = $this->pathModel;
              $program = ucfirst(strtolower($this->program_name)).EXT_PHP;
            break;

            case 'css':
              switch ($filename[1]) {
                case 'index':
                  $path = $this->pathViewCss;
                  $program = 'index.css';
                break;

                case 'detail':
                  $path = $this->pathViewCss;
                  $program = 'detail.css';
                break;

                case 'edit':
                  $path = $this->pathViewCss;
                  $program = 'edit.css';
                break;

                case 'novo':
                  $path = $this->pathViewCss;
                  $program = 'novo.css';
                break;
              }
            break;

            case 'js':
              switch ($filename[1]) {
                case 'index':
                  $path = $this->pathViewJs;
                  $program = 'index.js';
                break;

                case 'detail':
                  $path = $this->pathViewJs;
                  $program = 'detail.js';
                break;

                case 'edit':
                  $path = $this->pathViewJs;
                  $program = 'edit.js';
                break;

                case 'novo':
                  $path = $this->pathViewJs;
                  $program = 'novo.js';
                break;
              }
            break;

            case 'twig':
                switch ($filename[1]) {
                  case 'index':
                    $path = $this->pathView.strtolower($this->program_name).DS;
                    $program = 'index'.EXT_TWIG;
                  break;

                  case 'detail':
                    $path = $this->pathView.strtolower($this->program_name).DS;
                    $program = 'detail'.EXT_TWIG;
                  break;

                  case 'edit':
                    $path = $this->pathView.strtolower($this->program_name).DS;
                    $program = 'edit'.EXT_TWIG;
                  break;

                  case 'novo':
                    $path = $this->pathView.strtolower($this->program_name).DS;
                    $program = 'novo'.EXT_TWIG;
                  break;
                }
            break;

            case 'pdf':
                switch ($filename[1]) {
                  case 'pesquisa':
                    $path = $this->pathPdf;
                    $program = 'Pdf' . ucfirst(strtolower($this->program_name)) . 'Pesquisa' . EXT_PHP;
                  break;

                  case 'detail':
                    $path = $this->pathPdf;
                    $program = 'Pdf' . ucfirst(strtolower($this->program_name)) . 'Detail' . EXT_PHP;
                  break;
                }
            break;
          }
        } else {
            // arquivo não existe! mata tudo!
          return false;
        }

        // cria os arquivos
        $fp = fopen($path.$program, 'w+') or die('Não foi possível criar o arquivo: '.$program);
        fwrite($fp, $file);
        fclose($fp);
    }

        return true;
    }
}
