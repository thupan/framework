<?php

namespace Service\HTML;

use Service\Paginator;
use Service\Session;

class Table {
    protected static $table    = null;
    protected static $message  = null;
    protected static $paginate = true;
    public static $ignore = null;
    public static $only   = null;

    public static function Open($options = ['class' => 'table table-striped table-hover listview']) {
        foreach($options as $key => $value) {
            $attr .= $key.'="'.$value.'" ';
        }

        self::$table = "<table $attr>";
    }

    public static function Close() {
        self::$table .= '</table>';

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
            self::$table .= '<th>'.$field.'</th>';
        }

        if($id == 'tabela') $id = '';

        $name[0] = ($id) ? 'search-'.$id : 'search';
        $name[1] = ($id) ? 'search-'.$id.'-refresh' : 'search-refresh';
        $name[2] = ($id) ? 'form-fields-'.$id : 'form-fields';
        $name[3] = ($id) ? 'tabela-'.$id : 'tabela';
        $name[4] = ($id) ? 'imprimir-'.$id : 'imprimir';


        self::$table .= '<th class="text-center">AÇÕES</th>
                         </tr>
                         <tr class="search-fields">
                         <form class="'.$name[2].'">';

        foreach($data as $field => $key) {
         self::$table .= '<td><input name="'.$key.'" class="form-control Enter" type="text"/></td>';
        }

        // se houver botoes de acoes
        if($actions) {
            // seta o atributo para o elemento td
            self::$table .= "<td style='width:150px !important;'>";
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
            if(self::$message) {
                self::$table .= "<tr>
                                    <td colspan='100'>".self::$message."</td>
                                 </tr>";
            }

            $name[0] = ($options['id']) ? 'edit-'.$options['id'] : 'edit';
            $name[1] = ($options['id']) ? 'del-'.$options['id'] : 'delete';

            // prepara o conteudo dos campos para cada linha
            foreach($data as $index => $row) {

                // prepara atributos para os elementos tr e td, se solicitado.
                if($config) {
                    //dd($config);
                    foreach($config as $key => $configure) {

                        $k = explode(':', $key);

                        //
                        // switch($k[0]) {
                        //     case 'td':
                        //
                        //
                        //
                        //
                        //         foreach($configure as $cfg_key => $cfg_val) {
                        //             if($k[1]) {
                        //
                        //                 $attr_td = $cfg_key.'="'.$cfg_val.'" ';
                        //             } else {
                        //                 $attr_td = $cfg_key.'="'.$cfg_val.'" ';
                        //             }
                        //         }
                        //         //dd($attr_td);
                        //
                        //     break;
                        // }

                        // switch($key) {
                        //     case 'tr':
                        //         foreach($configure as $tr_key => $tr_value) {
                        //             $attr_tr .= $tr_key.'="'.$tr_value.'" ';
                        //         }
                        //     break;
                        //
                        //     case 'td':
                        //     //dd($configure);
                        //         foreach($configure as $td_key => $td_value) {
                        //             //dd($td_key);
                        //             if(is_array($td_value)) {
                        //                 //dd($td_value);
                        //             }
                        //             else {
                        //                 $attr_td .= $td_key.'="'.$td_value.'" ';
                        //             }
                        //
                        //         }
                        //     break;
                        // }

                    }
                }

                // seta atributo para o elemento tr
                self::$table .= "<tr $attr_tr>";
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

                    // retorna nome do campo limpo
                    $fname = explode('_', $field)[1];

                    if($v[1]) {
                        if($field === $v[1]) {
                            self::$table .= "<td data-label='$fname' $attr_td>$value</td>";
                            unset($attr_td);
                        } else {
                            self::$table .= "<td data-label='$fname'>$value</td>";
                        }
                    } else {
                        self::$table .= "<td data-label='$fname' $attr_td class='$field'>$value</td>";
                    }

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

                    // verifica quais os botoes serao adicionados
                    foreach($actions as $k => $action) {

                        if(!is_numeric($k)) {
                            if(is_bool($action)) {
                                $validarAcesso = $action;
                            }
                            $action = $k;
                        }

                        switch($action) {
                            case 'detail':
                                if($validarAcesso) {
                                    self::$table .= "<a href='$link/detail/$pk' class='btn btn-primary' alt='".$language[Session::get('s_locale')]['app']['details']."' title='".$language[Session::get('s_locale')]['app']['details']."'>
                                                        <span class='glyphicon glyphicon-list' aria-hidden='true'></span>
                                                        ".$language[Session::get('s_locale')]['app']['details']."
                                                     </a> ";
                                }
                            break;

                            case 'edit':
                                if($validarAcesso) {
                                    self::$table .= "<a href='$pk' class='btn btn-warning $name[0]' alt='".$language[Session::get('s_locale')]['app']['edit']."' title='".$language[Session::get('s_locale')]['app']['edit']."'>
                                                    <span class='glyphicon glyphicon-edit' aria-hidden='true'></span>
                                                 </a> ";
                                }
                            break;

                            case 'delete':
                                if($validarAcesso) {
                                        self::$table .= "<a href='$pk' class='btn btn-danger $name[1]' alt='".$language[Session::get('s_locale')]['app']['delete']."' title='".$language[Session::get('s_locale')]['app']['delete']."'>
                                                        <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                                                     </a> ";
                                }
                            break;

                            case 'print':
                                if($validarAcesso) {
                                    self::$table .= "<a href='$pk' class='btn btn-default' alt='".$language[Session::get('s_locale')]['app']['print']."' title='".$language[Session::get('s_locale')]['app']['print']."'>
                                                        <span class='glyphicon glyphicon-print' aria-hidden='true'></span>
                                                     </a> ";
                                }
                            break;

                            default:
                                self::$table .= "<a href='$pk' class='btn btn-default' alt='".$language[Session::get('s_locale')]['app']['btnUnknow']."' title='".$language[Session::get('s_locale')]['app']['btnUnknow']."'>
                                                    <span class='glyphicon' aria-hidden='true'>?</span>
                                                 </a> ";
                        }
                    }
                    // fecha a coluna dos botoes
                    self::$table .= '</td>';
                }
                // finaliza a linha
                self::$table .= '</tr>';
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

        return self::$message = "
                        <div class='alert alert-$type alert-dismissible fade in text-left' role='alert'>
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            $icon $message
                        </div>";
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
