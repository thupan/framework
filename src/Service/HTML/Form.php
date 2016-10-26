<?php

namespace Service\HTML;

use \Service\Session;

class Form {

    public static function formSearch($id, $url, $actions = [], $validate = true) {
        // carrega as configurações do frame para pegar o idioma
        $language = autoload_config();

        $name[0] = ($id) ? 'search-'.$id : 'search';
        $name[1] = ($id) ? 'form-fields-'.$id : 'form-fields';

        $form = "
          <div class='row'>
          <div class='container'>
            <div class='form-horizontal' role='form'>

              <div class='row row-header'>
                <div class='col-md-4 left'>";

                if(in_array('new', $actions)) {
                    if($validate) {

                        $form .= "
                        <a href='$url' class='btn btn-success'>
                          <span class='glyphicon glyphicon-plus' aria-hidden='true'></span> ".$language[Session::get('s_locale')]['app']['new']."
                        </a>";
                    }
                }

                if(in_array('print', $actions)) {
                    $form .= "

                      <a id='imprimir' class='btn btn-primary'>
                        <span class='glyphicon glyphicon-print' aria-hidden='true'></span> ".$language[Session::get('s_locale')]['app']['print']."
                      </a>";
                }
                  $form .= "
                </div>

                <div class='clearfix visible-xs visible-sm right mobile-search'>
                    <form class='$name[1]'>
                          <div class='form-group'>
                            <div class='input-group right' style='width:300px; margin-right:15px;'>
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
            </div>
          </div>
        </div>";

        echo $form;
    }

}
