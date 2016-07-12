<?php

namespace Service;

use \Service\Paginator;

class XHR
{
    public static $ignore = [];
    public static $only = [];

    protected static $alert = false;
    public static $validator = true;

    public static function alert($message, $alert = 'info')
    {
        switch ($alert) {
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

        self::$alert = "
                        <div class='alert alert-$alert alert-dismissible fade in' role='alert'>
                            $icon
                            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                <span aria-hidden='true'>&times;</span>
                            </button>
                            $message
                        </div>";

        return self::$alert;
    }

    public static function getBreadCrumb($title)
    {
        $local = explode('/', substr(ROUTER_REQUEST, 0, strlen(URL)));

        !$local[0] ? $local[0] = $title : false;

        $total = (int) count($local) - (int) 1;

        for ($i = 0; $i < count($local); ++$i) {
            $tmp .= $local[$i].'/';
            $local[$i] = (is_numeric($local[$i])) ? '#'.$local[$i] : $local[$i];
            $active = ($i == 0) ? " href='".URL."{$tmp}' " : " style='text-decoration:none; color:#9c9c9c; ' ";
            $name = ($i == 0) ? $title : $local[$i];
            $pwd .= "<li><a $active >$name</a></li>";
        }

        $title = ucfirst($title);

        return "<div class='row'>
                <div class='container'>
                <h1>$title</h1>
                <div class='row'>
                    <ol class='breadcrumb'>
                        <li>
                            <a href='#' onclick='javascript:history.go(-1); return false;'>
                                <span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span>
                            </a>
                        </li>
                        $pwd
                    </ol>
                </div>
                <hr/>
                </div>
                </div>";
    }

    /*=============================================>>>>>
    = XHR FUNCTIONS HERE!!! =
    ===============================================>>>>>*/

    public static function validator()
    {
        return self::$validator;
    }

    public static function tablex($data = array(), $action = null, $paginate = true)
    {
        $paginator = new Paginator();

        if ($paginate) {
            $paginator->paginate($data);
        }

        $fields = (int) 0;
        $item = (int) 0;

        if ($action) {
            $reg = explode(':', $action);
            $ids = explode('-', $reg[2]);

            $controller = $reg[0];

            switch ($reg[1]) {
        case 'edit':
          $edit = true;
          $delete = false;
        break;

        case 'del':
          $delete = true;
          $edit = false;
        break;

        case 'edit.del':
          $edit = true;
          $delete = true;
        break;
      }
        }

        if (self::$alert) {
            $table .= "<tr><td colspan='15'>".self::$alert.'</td></tr>';
        }

        if ($data) {
            foreach ($data as $key => $field) {

        // só carrega os campos selecionados
        // foreach(array_keys($field) as $r => $c) {
        //     if(!in_array($c, array_keys(self::$only))) {
        //         unset($field[$c]);
        //     }
        // }

        // remove só os campos selecionados
        foreach (self::$ignore as $k => $key) {
            unset($field[$key]);
        }

                ++$fields;

                foreach ($ids as $k => $i) {
                    $prepare .= "\$data[$item]['$i']";
                    $prepare = $prepare.".'-'.";
                }

                $prepare = rtrim($prepare, ".'-'.");

                eval('$id = '.$prepare.';');

                unset($prepare);

                ++$item;

                $table .= '<tr>';

                foreach ($field as $key => $value) {
                    if (in_array($key, array_keys(self::$only))) {
                        $attr = explode(',', self::$only[$key]);
                        $attr = implode(' ', $attr);
                    }

                    $key = explode('_', $key);
                    $key = ($key[0] == 'ID') ? '#' : $key[1];

                    $table .= "<td data-label='{$key}' $attr>{$value}</td>";

                    unset($attr);
                }

                if (self::validator()) {
                    $table .= "<td align='center'>";

                    if ($edit) {
                        if ($reg[3] != $item) {
                            $table .= "<a href='$id' class='btn btn-warning edit-$controller'>
                                <span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>
                           </a> &nbsp;";
                        }
                    }

                    if ($delete) {
                        if ($reg[4] != $item) {
                            $table .= "<a href='$id' class='btn btn-danger delete del-$controller'>
                              <span class='glyphicon glyphicon-trash' aria-hidden='true'></span>
                           </a>";
                        }
                    }

                    $table .= '</td>';
                }

                $table .= '</tr>';
            }
        } else {
            $table .= "<tr>
                            <td colspan='100' align='center' class='nohover' style='padding:30px; font-size:15px'>
                                <span class='glyphicon glyphicon-exclamation-sign'></span> Nenhum registro foi encontrado.
                            </td>
                       </tr>";
        }

        if ($paginate) {
            $table .= "<tr class='tfoot'><td colspan='100' class='nohover'>{$paginator->pages()}</td></tr>";
        }

        echo $table;
    }

    // XHR HTML
    public static function table($data = array(), $detail = null, $paginate = true)
    {
        $paginator = new Paginator();

        if ($paginate) {
            $paginator->paginate($data);
        }

        $fields = (int) 0;
        $item = (int) 0;

        if ($detail) {
            $reg = explode(':', $detail);
            $ids = explode('-', $reg[1]);
        }

        if (self::$alert) {
            $table .= "<tr><td colspan='100'>".self::$alert.'</td></tr>';
        }

        if ($data) {
            foreach ($data as $key => $field) {
                if (self::$ignore) {
                    foreach (self::$ignore as $k => $key) {
                        unset($field[$key]);
                    }
                }
                ++$fields;

                foreach ($ids as $k => $i) {
                    $prepare .= "\$data[$item]['$i']";
                    $prepare = $prepare.".'-'.";
                }

                $prepare = rtrim($prepare, ".'-'.");

                eval('$id = '.$prepare.';');
                unset($prepare);

                ++$item;

                $table .= '<tr>';
                foreach ($field as $key => $value) {
                    $key = explode('_', $key)[1];
                    $table .= "<td data-label='".$key."'>{$value}</td>";
                }

                if ($detail) {
                    $table .= "<td align='center' width='150'>
                        <a href='".URL.$reg[0]."/detail/{$id}' class='btn btn-info'>
                        <span class='glyphicon glyphicon-list' aria-hidden='true'></span>
                          <span class='hidden-xs'>Detalhes</span>
                        </a>
                      </td>";
                }

                $table .= '</tr>';
            }
        } else {
            $table .= "<tr>
                            <td colspan='100' align='center' class='nohover' style='padding:30px; font-size:15px'>
                                <span class='glyphicon glyphicon-exclamation-sign'></span> Nenhum registro foi encontrado.
                            </td>
                       </tr>";
        }

        if ($paginate) {
            $table .= "<tr class='tfoot'><td colspan='100' class='nohover'>{$paginator->pages()}</td></tr>";
        }

        echo $table;
    }

    public static function ignore($field = array())
    {
        self::$ignore = $field;
    }

    public static function only($field = array())
    {
        foreach ($field as $key => $value) {
            // força a chave do array ser o campo
          $key = (is_integer($key)) ? $value : $key;
          // se nao tiver valor o value vai ser o mesmo nome da key
          self::$only[$key] = $value;
        }
    }
}
