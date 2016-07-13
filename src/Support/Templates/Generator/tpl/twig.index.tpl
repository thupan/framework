{#************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * CRIADOR POR: {%GC_DEVELOPER%}@{%GC_MACHINE%}
 ************************************************************#}

{% extends "index.twig" %}
{% block content %}

{{ flash | raw }}

<div class="row">
  <div class="container">
    <div class="form-horizontal" role="form">

      <div class="row row-header">
        <div class="col-md-4 left">
          {% if validarAcesso() %}
            <a href="{{URL}}{%controller_name%}/novo" class="btn btn-success">
              <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> NOVO
            </a>
          {% endif %}

          <a id="imprimir" class="btn btn-primary">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span> IMPRIRMIR
          </a>
        </div>
        <div class="clearfix visible-xs visible-sm right mobile-search">

          <div class="form-group">
            <div class="input-group right" style="width:300px; margin-right:15px;">
              <input type="text" class="form-control" id="TX_PESQUISA" placeholder="Pesquisa Rápida">
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
