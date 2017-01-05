{#************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************#}

{% extends "index.twig" %}
{% block content %}

{{ flash | raw }}

<!-- botão novo, botões de exportação, pesquisa mobile -->
<div class="row">
    <div class="container">
        <div class="form-horizontal" role="form">
            <div class="row row-header">
                <!-- botão de novo -->
                <div class=" left" style="margin-left:20px">
                    {% if validarAcesso() %}
                        <a href="{{URL}}{%controller_name%}/novo" class="btn btn-success">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            NOVO
                        </a>
                    {% endif %}
                </div>
                <!-- botões de exportação -->
                <div class="right" style="margin-right:20px">
                    <div class="btn-group" role="group" aria-label="...">
                        <button id="imprimir" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                            PDF
                        </button>
                        <button id="imprimir-xls" type="button" class="btn btn-default">
                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                            XLS
                        </button>
                    </div>
                </div>
                <!-- barra de pesquisa mobile -->
                <div class="clearfix visible-xs visible-sm right mobile-search">
                    <div class="row" style='padding:10px;margin-top:10px'>
                        <div class="container">
                            <div class="form-group col-md-12">
                                <div class="input-group right">
                                    <input type="text" class="form-control Enter" id="TX_PESQUISA" placeholder="Pesquisa Rápida">
                                    <div class="input-group-btn">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary search">
                                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- tabela de consulta principal -->
<div class="row">
  <div class="container">
    <table class="listview table table-striped table-hover">
      <thead id="tabela-header">
          {%tableHeader%}
      </thead>
      <tbody id="tabela"></tbody>
    </table>
  </div>
</div>

{% endblock %}
