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
                    <h3 class="panel-title">Detalhe do {{controller}}</h3>
                </div>
                <div class="panel-body">
                    {%HTMLDetail%}

                    {% if validarAcesso() %}
                        <div class="right">
                            {%HTMLDetailBtn%}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>

    {# ABAS #}
    <div class="row">
        <div class="container">

            <div class="painel-nav">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a href="#aba1" data-toggle="tab" class="active">ABA 1</a>
                    </li>
                    <li>
                        <a href="#aba2" data-toggle="tab">ABA 2</a>
                    </li>
                    {% if validarAcesso() %}
                        <li>
                            <a href="#aba3" data-toggle="tab">ABA 3</a>
                        </li>
                    {% endif %}

                </ul>
                <div class="tab-content">
                    <div id="aba1" class="tab-pane active">
                            {# CONTEUDO ABA 1 #}

                            <div class="row">
                                {% if validarAcesso() %}
                                    <button id="form-aba1-grid-show" class="btn btn-primary">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        {{langApp.new}}
                                    </button>

                                    <div id="form-aba1-grid"  style="display:none; width:100%; padding-top:10px; padding-bottom:10px;">
                                        <form class="form-aba1" role="form" novalidate>

                                            <input type="hidden" name="edit" id="edit" value="" />
                                            <input type="hidden" name="CAMPO_ESCONDIDO" value="{{CAMPO_ESCONDIDO}}" id="CAMPO_ESCONDIDO"/>

                                            <div class="form-group">
                                                <label for="CAMPO_ABA1" class="obrigatorio">CAMPO_ABA1</label>
                                                <input type="text" class="form-control" name="CAMPO_ABA1" id="CAMPO_ABA1" placeholder="Entre com o valor" required>
                                            </div>


                                            <a id="form-aba1-grid-cancel" class="btn btn-default left">
                                                <span class='glyphicon glyphicon-remove' aria-hidden='true'></span>
                                                {{langApp.cancel}}
                                            </a>

                                            <button id="form-aba1-add" class="btn btn-success right">
                                                <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                                                {{langApp.save}}
                                            </button>

                                        </form>
                                    </div>
                                {% endif %}
                            </div>

                            {# TABELA DE PESQUISA #}
                            {{table('aba1', [
                            	'#:TABELA.CAMPO_ABA1:ANY',
                           	    'CAMPO:TABELA.CAMPO_ABA1:ANY',
                            ], ['search', 'reset']) | raw}}
                            {# FIM TABELA PESQUISA#}
                            {# FIM CONTEUDO EXEMPLO ABA 1 #}
                    </div>

                    <div id="aba2" class="tab-pane">
                        <div class="row">
                            {# CONTEUDO ABA 2 #}
                        </div>
                    </div>

                    {% if validarAcesso() %}
                        <div id="aba3" class="tab-pane">
                            <div class="row">
                                <div class="col-md-12">

                                    {# CONTEUDO ABA 3 #}

                                </div>
                            </div>
                        </div>
                    {% endif %}

                </div>
            </div>

        </div>
    </div>
{% endblock %}
