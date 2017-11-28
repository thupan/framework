{#************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************#}

 {# ignora o menu na tela #}
 {# set disable_menu = true #}
 {# ignora o breadcrumb na tela #}
 {# set disable_breadcrumb = true #}

 {% extends "_templates/pmm/index.twig" %}
 {% block content %}

     {{ flash | raw }}

     {# DETALHES #}
     <div class="row">
         <div class="container">
             <div class="panel panel-default">
                 <div class="panel-heading">
                     <h3 class="panel-title">Editar {{controller}}</h3>
                 </div>
                 <div class="panel-body">
                     <form class="form-edit-{%controller_name%}" method="post" action="{{URL}}{%controller_name%}/save" novalidate="novalidate">
                         <input type="hidden" name="edit" id="edit" value="{{{%tablePk%}}}" />
                         <input type="hidden" name="{%tablePk%}" id="{%tablePk%}" value="{{{%tablePk%}}}" />

                        {%HTMLEdit%}

                        <div class="left">
                            <a class="btn btn-default cancel">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                {{langApp.cancel}}
                            </a>
                        </div>

                         <div class="right">
                             <a class="btn btn-success validate">
                                 <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                 {{langApp.save}}
                             </a>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>

 {% endblock %}
