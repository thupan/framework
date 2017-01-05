<?php

namespace Service;

use Service\HTML\Table;

class XHR
{
    public static $ignore = null;
    public static $only   = null;

    protected static $alert = false;
    public static $validator = true;

    public static $paginate = true;

    public static function alert($message, $type = 'info')
    {
        return Table::Alert($message, $type);
    }

    public static function getBreadCrumb($title)
    {
        $local = explode('/', substr(ROUTER_REQUEST, 0, strlen(ROUTER_REQUEST)));

        !$local[0] ? $local[0] = $title : false;

        $total = (int) count($local) - (int) 1;

        for ($i = 0; $i < count($local); ++$i) {
            $tmp .= $local[$i].'/';
            $local[$i] = (is_numeric($local[$i])) ? '#'.$local[$i] : $local[$i];
            $active = ($i == 0) ? " href='".URL."{$tmp}' " : " style='text-decoration:none; color:#9c9c9c; ' ";
            $name = ($i == 0) ? $title : $local[$i];

            $name = ($name == 'detail') ? 'detalhes' : $name;
            $name = ($name == 'edit')   ? 'edição' : $name;
            

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

    public static function table($id, $data, $action = [], $pkey = [], $config = [])
    {
        Table::$paginate = self::$paginate;

        if(self::$ignore) {
            Table::ignore(self::$ignore);
        } else if(self::$only) {
            Table::only(self::$only);
        }

        // prepara a tabela com o dados passados.
        Table::Rows(is_array($id) ? $id : ['id' => $id], $data, $action, $pkey, $config);
        // retorna a tabela montanda.
        echo Table::Show();
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
