/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * CRIADOR POR: {%GC_DEVELOPER%}@{%GC_MACHINE%}
 ************************************************************/
 
$(document).ready(function() {
    // funções globais da tela.
    //

    // funções que serão executados no método principal.
    if(method === 'index') {
        carregaTabela("#tabela", url + "{%controller_name%}/xhrPesquisar");
        enviarDadosTabela(".search", ".form-fields", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        enviarDadosTabela(".search-refresh", "data", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        enviarDadosPdf("#imprimir", ".form-fields", url + "{%controller_name%}/xhrImprimirPesquisa");
    }

    if(method === 'detail') { }
    if(method === 'edit')   { }
    if(method === 'novo')   { }
});
