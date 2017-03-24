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

 {{header(URL ~ controller ~ '/novo', ['pdf', 'xls'], validarAcesso()) | raw}}

 {{table('tabela', [
 {%tableHeader%}
 ], ['search', 'reset']) | raw}}

 {% endblock %}
