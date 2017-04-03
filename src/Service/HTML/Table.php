<?php

namespace Service\HTML;

use Service\Paginator;
use Service\Session;

class Table {
    protected static $table    = null;
    protected static $message  = null;
    public static $paginate = true;
    public static $ignore = null;
    public static $only   = null;
    public static $columns_data  = [];

    public static function formSearch($id, $url =null , $actions = [], $validate = true) {
        // carrega as configurações do frame para pegar o idioma
        $language = autoload_config();

        $name[0] = (!$id || $id == 'tabela') ? 'search'      : 'search-'.$id;
        $name[1] = (!$id || $id == 'tabela') ? 'form-fields' : 'form-fields-'.$id;

        $form = "
          <div class='row'>
          <div class='container'>
                <div class='clearfix visible-xs visible-sm right mobile-search'>
                    <form class='$name[1]'>
                          <div class='form-group'>
                            <div class='input-group '>
                              <input type='text' class='form-control Enter' id='TX_PESQUISA' name='TX_PESQUISA' placeholder='".$language[Session::get('s_locale')]['app']['search_']."'>
                              <div class='input-group-btn'>
                                  <div class='btn-group' role='group'>
                                    <button type='button' class='btn btn-primary $name[0]'>
                                      <span class='glyphicon glyphicon-search' aria-hidden='true'></span>
                                    </button>
                                  </div>
                              </div>
                            </div>
                          </div>
                    </form>
                </div>
          </div>
        </div>";

        echo $form;
    }

    public static function formHeader($url = null, $buttons = [], $validate = true) {
        $language = autoload_config();

        if($validate && $url) {
            $btn_new = "

            <a href='$url' class='btn btn-success'>
                <span class='glyphicon glyphicon-plus' aria-hidden='true'></span>
                ".$language[Session::get('s_locale')]['app']['new']."
            </a>

            ";
        }

        foreach($buttons as $but) {
            switch($but) {
                case 'pdf':
                $btns .= "

                <button id='imprimir' type='button' class='btn btn-default'>
                    <span class='fa fa-2x fa-file-pdf-o' aria-hidden='true' style='color:red' alt='Exportar para pdf' title='Exportar para pdf'></span>
                </button>

                ";
                break;

                case 'xls':
                $btns .= "

                <button id='imprimir-xls' type='button' class='btn btn-default'>
                    <span class='fa fa-2x fa-file-excel-o' aria-hidden='true' style='color:green' alt='Exportar para excel' title='Exportar para excel'></span>
                </button>

                ";
                break;
            }
        }

        $html .= "

        <div class='row row-header'>
            <div class='container'>
                <!-- botão de novo -->
                <div class='left'>
                $btn_new
                </div>
                <!-- botões de exportação -->
                <div class='right' style='margin-right:0px'>
                    <div class='btn-group' role='group' aria-label='...'>
                        $btns
                    </div>
                </div>
            </div>
        </div>

        ";

        return $html;
    }

    public static function Open($options = ['class' => 'table table-striped table-hover listview']) {
        foreach($options as $key => $value) {
            $attr .= $key.'="'.$value.'" ';
        }

        self::$table = "
        <div class='table-responsive'>

        <table $attr>";
    }

    public static function Close() {
        self::$table .= '</table> </div>';

        echo self::$table;
    }

    public static function Header($options = [], $id = false, $data = [], $actions = []) {
        // carrega as configurações do frame para pegar o idioma
        $language = autoload_config();
        // prepara os atributos para o thead
        foreach($options as $k => $v) {
            $attr .= $k.'="'.$v.'" ';
        }

        self::$table .= "<thead $attr>
                         <tr>";

        foreach($data as $field => $key) {
            if($field != 'hidden') {
                self::$table .= '<th>'.$field.'</th>';
            }
        }

        if($id == 'tabela') $id = '';

        $name[0] = ($id) ? 'search-'.$id : 'search';
        $name[1] = ($id) ? 'search-'.$id.'-refresh' : 'search-refresh';
        $name[2] = ($id) ? 'form-fields-'.$id : 'form-fields';
        $name[3] = ($id) ? 'tabela-'.$id : 'tabela';
        $name[4] = ($id) ? 'imprimir-'.$id : 'imprimir';
        $name[5] = ($id) ? 'Enter-'.$id : 'Enter';

        self::$table .= '<th class="text-center">AÇÕES</th>
                         </tr>
                         <tr class="search-fields">
                         <form class="'.$name[2].'">';

        foreach($data as $field => $key) {
            $combo = explode('@', $key);

            $key    = ($key === ":") ? false : $key;

            $filter = ($key) ? true : false;

            if($combo[1]) {
                $key  = $combo[0];
                $data = json_decode(base64_decode($combo[2]), true);
                if($data) {
                    $options = "<option value=''></option>";
                    foreach($data as $s_index => $s_array) {
                        $options .= "<option value='".$s_array['ID']."'>".$s_array['TEXT']."</option>";
                    }
                } else {
                    $options = "<option selected>Seu array não retornou dados</option>";
                }
                self::$table .= '<td><select name="'.$key.'" class="form-control-select2 '.$name[5].'">'.$options.'</select></td>';
            } else if($key && $key !== ":") {
                self::$table .= '<td><input name="'.$key.'" class="form-control '.$name[5].'" type="text"/></td>';
            } else {
                break;
            }

        }

        // se houver botoes de acoes
        if($actions && $filter) {
            // seta o atributo para o elemento td
            self::$table .= "<td style='width:150px !important;'>";
            self::$table .= '<div class="btn-group" role="group" aria-label="...">';
            // verifica quais os botoes serao adicionados
            foreach($actions as $action) {
                switch($action) {
                    case 'search':
                        self::$table .= "<button type='button' class='btn btn-primary $name[0]' alt='".$language[Session::get('s_locale')]['app']['search']."' title='".$language[Session::get('s_locale')]['app']['search']."'>
                                            <span class='glyphicon glyphicon-search' aria-hidden='true'></span>
                                         </button> ";
                    break;

                    case 'reset':
                        self::$table .= "<button type='button' class='btn btn-default $name[1]' alt='".$language[Session::get('s_locale')]['app']['reload']."' title=' ".$language[Session::get('s_locale')]['app']['reload']."'>
                                            <span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>
                                         </button> ";
                    break;

                    case 'print':
                        self::$table .= "<button type='button' class='btn btn-default' id='$name[4]' alt='".$language[Session::get('s_locale')]['app']['print']."' title='".$language[Session::get('s_locale')]['app']['print']."'>
                                            <span class='glyphicon glyphicon-print' aria-hidden='true'></span>
                                         </button> ";
                    break;

                    default:
                        self::$table .= "<button type='button' class='btn btn-default' alt='".$language[Session::get('s_locale')]['app']['btnUnknow']."' title='".$language[Session::get('s_locale')]['app']['btnUnknow']."'>
                                            <span class='glyphicon' aria-hidden='true'>?</span>
                                         </button>";
                }
            }
            self::$table .= '</div>';
            // fecha a coluna dos botoes
            self::$table .= '</td>';
        }

        self::$table .= '</form></tr>';
    }

    public static function Rows($options = [], $data, $actions = [], $pkey = [], $config = []) {
        // carrega as configurações do frame para pegar o idioma
        $language = autoload_config();

        // se houver registros
        if($data) {

            // prepara os dados para paginação, se solicitado.
            if(self::$paginate) {
                $paginator = new Paginator();
                $paginator->paginate($data);
            }

            // mostra uma mensagem de alerta se existir.
            // if(self::$message) {
            //     self::$table .= "<tr>
            //                         <td colspan='100%'>".self::$message."</td>
            //                      </tr>";
            // }

            $name[0] = ($options['id']) ? 'edit-'.$options['id'] : 'edit';
            $name[1] = ($options['id']) ? 'del-'.$options['id'] : 'delete';

            // prepara o conteudo dos campos para cada linha
            foreach($data as $index => $row) {
               $count = 0;

                // pega o conteudo de cada campo
                foreach($row as $field => $value) {
                    // verifica se existe campos para serem eliminados
                    if(self::$ignore) {
                        // remove os campos ignorados
                        if(in_array($field, self::$ignore)) {
                            continue;
                        }
                    } else if(self::$only) {
                        // remove todos os campos que nao forem passados
                        if(!in_array($field, self::$only)) {
                            continue;
                        }
                    }

                  if($count==0){
                    // Configuração de atributos pra o tr da tabela
                    if($config) {

                      if($config['tr']){
                        if($config['tr'][$field.':'.$value]){
                          foreach($config['tr'][$field.':'.$value] as $key => $vl){
                            $attr_tr .= $key.'="'.$vl.'" ';
                          }
                        }else{
                          $attr_tr='';
                        }
                      }
                    }
                    // seta atributo para o elemento tr
                    self::$table .= "<tr $attr_tr>";

                  }


                    // retorna nome do campo limpo
                    $fname = explode('_', $field)[1];

                    // Configuração de atributos pra o td da tabela
                    if($config) {
                      if($config['td']){

                        // dd($config['td'][$field]);
                        if($config['td'][$field]){
                          foreach($config['td'][$field] as $key => $vl){
                            $k = explode(':',$key);

                            if($k[0]!='value'){
                              $attr_td .= $key.'="'.$vl.'" ';
                            }
                          }
                          if($config['td'][$field]['value:'.strtoupper($value)]){
                            foreach($config['td'][$field]['value:'.strtoupper($value)] as $key_1 => $vl_1){
                                $attr_td .= $key_1.'="'.$vl_1.'" ';
                            }
                          }

                        }else{
                          $attr_td='';
                        }
                      }
                      // dd($attr_td);
                    }

                    if($v[1]) {
                        if($field === $v[1]) {
                            self::$table .= "<td data-label='$fname' $attr_td class='$field'>$value</td>";
                            $columns_x[] = $field;
                            unset($attr_td);
                        } else {
                            self::$table .= "<td data-label='$fname' class='$field'>$value</td>";
                            $columns_x[] = $field;
                        }
                    } else {
                        self::$table .= "<td data-label='$fname' $attr_td class='$field'>$value</td>";
                        $columns_x[] = $field;
                    }
                    $count++;
                }

                // se houver botoes de acoes
                if($actions) {

                    // verifica se foi passado as chaves da tabela
                    if($pkey) {
                        // pega todas as chaves passadas
                        foreach($pkey as $pk) {
                            $arr[] = $row[$pk];
                        }
                        // cria uma string separando por ífen
                        $pk = implode('-', $arr);
                        // limpa o array
                        unset($arr);
                    }

                    // seta o atributo para o elemento td
                    self::$table .= "<td $attr_td>";

                    $validarAcesso = true;
                    $link = URL . \Routing\Router::getControllerName();

                        self::$table .= '<div class="btn-group" role="group" aria-label="...">';
                    // verifica quais os botoes serao adicionados
                    foreach($actions as $k => $action) {
                        if(!is_numeric($k)) {
                            if(is_bool($action)) {
                                $validarAcesso = $action;
                            } else if(is_array($action)) {
                                $m_name = $action;
                            } else {
                                $m_name = $action;
                            }
                            $action = $k;
                        }

                        switch($action) {
                            case 'detail':
                                if($validarAcesso) {
                                    self::$table .= "<a  href='$link/detail/$pk' class='btn btn-primary' alt='".$language[Session::get('s_locale')]['app']['details']."' title='".$language[Session::get('s_locale')]['app']['details']."'>
                                                        <span class='glyphicon glyphicon-list' aria-hidden='true'></span>
                                                        ".$language[Session::get('s_locale')]['app']['details']."
                                                     </a> ";
                                }
                            break;

                            case 'modal':

                                $m = explode(':', array_keys($m_name)[0]);
                                if (is_callable($m_name[array_keys($m_name)[0]])){

                                     $valida = $m_name[array_keys($m_name)[0]]($row);

                                } else if(is_bool($m_name[array_keys($m_name)[0]])) {

                                     $valida = $m_name[array_keys($m_name)[0]];
                                } else {

                                     $valida = true;
                                }

                                if($valida){
                                    self::$table .= "<button type='button' data-href='$pk' data-toggle='modal' data-target='#$m[0]' class='btn btn-info modal-$m[0]' alt='$m[1]' title='$m[1]'>
                                                                <span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span>
                                                            </button> ";
                                }


                                // if($m_name) {
                                //     if(is_array($m_name)) {
                                //         if(is_callable(array_values($m_name)[0])) {
                                //             $vArgs = func_get_args();
                                //             dd($vArgs);
                                //             //
                                //             // foreach(call_user_func_array(array_values($m_name)[0], $vArgs[1]) as $iix => $aar) {
                                //             //     foreach($arr as $kk => $vv) {
                                //             //         if($kk == $field) {
                                //             //
                                //             //         }
                                //             //     }
                                //             // }
                                //
                                //             dd( call_user_func_array(array_values($m_name)[0], $vArgs) );
                                //         } else {
                                //             $m = explode$row (':', array_keys($m_name)[0]);
                                //             $validarAcessoModal = (bool) array_values($m_name)[0];
                                //         }
                                //     } else {
                                //         $m = explode(':', $m_name);
                                //         $validarAcessoModal = true;
                                //     }
                                //     if($validarAcessoModal) {
                                //         self::$table .= "<button type='button' data-href='$pk' data-toggle='modal' data-target='#$m[0]' class='btn btn-info modal-$m[0]' alt='$m[1]' title='$m[1]'>
                                //                             <span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span>
                                //                         </button> ";
                                //     }
                                // }
                            break;

                            // icon => 'nome_id:botao_ico.botao_tipo'
                            case 'icon':
                                if(!is_array($m_name)) {
                                    $m    = explode(':', $m_name);
                                    $w    = explode('.', $m[1]);
                                    $w[1] = !$w[1] ? 'warning' : $w[1];

                                    self::$table .= "<button type='button' data-href='$pk' class='btn btn-$w[1] $m[0]' alt='$m[2]' title='$m[2]'>
                                                    <span class='glyphicon glyphicon-$w[0]' aria-hidden='true'></span>
                                                    <span>$m[2]</span>
                                                 </button> ";
                                } else {
                                    foreach($m_name as $i => $v) {
                                        if(is_array($v)) {
                                            $m    = explode(':', $i);
                                            $w    = explode('.', $m[1]);
                                            $w[1] = !$w[1] ? 'warning' : $w[1];

                                            self::$table .= "<div class='btn-group'>
                                                <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                                    <span class='glyphicon glyphicon-$w[0]' aria-hidden='true'></span>
                                                <span>$m[2]</span>
                                                <span class='caret'></span>
                                                </button>
                                                <ul class='dropdown-menu' style='left:auto; right:0;' role='menu'>";

                                            foreach($v as $in => $va){
                                                $m    = explode(':', $va);
                                                $w    = explode('.', $m[1]);
                                                $w[1] = !$w[1] ? 'warning' : $w[1];

                                                self::$table .= "<li><a class='{$m[0]}' data-href='$pk'>
                                                <span class='glyphicon glyphicon-$w[0]' aria-hidden='true'></span>
                                                <span>$m[2]</span></a>
                                                </li>";
                                            }

                                            self::$table  .= "</ul>

                                            </div>";

                                        } else {
                                            $m    = explode(':', $v);
                                            $w    = explode('.', $m[1]);
                                            $w[1] = !$w[1] ? 'warning' : $w[1];

                                            self::$table .= "<button type='button' data-href='$pk' class='btn btn-$w[1] $m[0]' alt='$m[2]' title='$m[2]'>
                                                            <span class='glyphicon glyphicon-$w[0]' aria-hidden='true'></span>
                                                            <span>$m[2]</span>
                                                         </button> ";
                                        }
                                    }
                                }
                            break;

                            case 'edit':
                                if($validarAcesso) {
                                    self::$table .= "<button type='button' href='$pk' class='btn btn-warning $name[0]' alt='".$language[Session::get('s_locale')]['app']['edit']."' title='".$language[Session::get('s_locale')]['app']['edit']."'>
                                                    <span class='glyphicon glyphicon-edit' aria-hidden='true'></span>
                                                 </button> ";
                                }
                            break;

                            case 'delete':
                                if($validarAcesso) {
                                        self::$table .= "<button type='button' href='$pk' class='btn btn-danger $name[1]' alt='".$language[Session::get('s_locale')]['app']['delete']."' title='".$language[Session::get('s_locale')]['app']['delete']."'>
                                                        <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                                                     </button> ";
                                }
                            break;

                            case 'print':
                                if($validarAcesso) {
                                    self::$table .= "<button type='button' href='$pk' class='btn btn-default' alt='".$language[Session::get('s_locale')]['app']['print']."' title='".$language[Session::get('s_locale')]['app']['print']."'>
                                                        <span class='glyphicon glyphicon-print' aria-hidden='true'></span>
                                                     </button> ";
                                }
                            break;

                            default:
                                self::$table .= "<button type='button' href='$pk' class='btn btn-default' alt='".$language[Session::get('s_locale')]['app']['btnUnknow']."' title='".$language[Session::get('s_locale')]['app']['btnUnknow']."'>
                                                    <span class='glyphicon' aria-hidden='true'>?</span>
                                                 </button> ";
                        }

                    }
                                            self::$table .= '</div>';
                    // fecha a coluna dos botoes
                    self::$table .= '</td>';
                }
                // finaliza a linha
                self::$table .= '</tr>';
            }

            if(self::$columns_data) {
                //dd(self::$columns_data);
                $columns_x = array_unique($columns_x);

                foreach(self::$columns_data as $index => $row) {
                    $td_null = 0;
                    self::$table .= '<tr>';

                    foreach($row['data'] as $f_y_k => $f_y_v) {
                        foreach($columns_x as $i_x_k => $f_x_k) {
                            if($f_y_k == $f_x_k) {
                                unset($columns_x[$i_x_k]);
                                $td_found .=  '<td>' . $f_y_v . '</td>';
                                if($td_null == 0) {
                                    self::$table .= $td_found;
                                    $td_null  = 0;
                                    $td_found = '';
                                    break;
                                } else {
                                    unset($columns_x[$i_x_k]);
                                    if(!$title) {
                                        $title     = $row['title'];
                                        $title_cfg = " style='text-align:right' ";
                                    } else {
                                        $title = null;
                                        $title_cfg = null;
                                    }
                                    self::$table .= "<td colspan='$td_null' $title_cfg>$title</td>";
                                    self::$table .= $td_found;
                                    $td_null  = 0;
                                    $td_found = '';
                                    break;
                                }
                            } else {
                                $td_null++;
                                unset($columns_x[$i_x_k]);
                            }

                        }
                    }
                    self::$table .= '<td></td></tr>';
                }
            }

            if(self::$paginate) {
                $paginator->id = $options['id'];
                self::$table .= "<tr class='tfoot'><td colspan='100' class='nohover'>".$paginator->pages()."</td></tr>";
            }
        } else {
            self::$table .= "<tr>
                                <td colspan='100' align='center' class='nohover' style='padding:30px; font-size:15px'>
                                    <span class='glyphicon glyphicon-exclamation-sign'></span> ".$language[Session::get('s_locale')]['app']['searchnotfound']."
                                </td>
                            </tr>";
        }
    }

    public static function Body($options = [], $data = null) {
        // prepara os atributos para o tbody
        foreach($options as $k => $v) {
            $attr .= $k.'="'.$v.'" ';
        }

        // seta o atributo
        self::$table .= "<tbody $attr>$data</tbody>";
    }

    public static function Footer($options = [], $data = null) {
        // prepara os atributos para o tbody
        foreach($options as $k => $v) {
            $attr .= $k.'="'.$v.'" ';
        }
        // seta o atributo
        self::$table .= "<tfooter $attr>$data</tfooter>";
    }

    public static function Alert($message, $type = 'info') {
        switch ($type) {
            case 'info':
                $icon = '<span class="glyphicon glyphicon-info-sign"></span>';
            break;

            case 'warning':
                $icon = '<span class="glyphicon glyphicon-exclamation-sign"></span>';
            break;

            case 'danger':
                $icon = '<span class="glyphicon glyphicon-remove-sign"></span>';
            break;

            case 'success':
                $icon = '<span class="glyphicon glyphicon-ok-sign"></span>';
            break;
        }

        self::$message = "
                <div class='row'>
                <div class='container'>
                        <div class='alert alert-$type alert-dismissible fade in text-left' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            $icon $message
                        </div>
                </div>
                </div>";

        return self::$message;
    }

    public static function Show() {
        return self::$table;
    }

    public static function ignore($field = [])
    {
        self::$ignore = $field;
    }

    public static function only($field = [])
    {
        self::$only = $field;
    }
}
