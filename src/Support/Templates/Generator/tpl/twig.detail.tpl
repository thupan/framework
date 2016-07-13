{#************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * CRIADOR POR: {%GC_DEVELOPER%}@{%GC_MACHINE%}
 ************************************************************#}

{% extends "index.twig" %}
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
                        <div class="row">
                            {# CONTEUDO ABA 1 #}
                        </div>
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
