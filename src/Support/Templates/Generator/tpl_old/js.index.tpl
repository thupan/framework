/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/


$(document).ready(function(d) {

    // chama o metodo xhrPesquisar do controlador {%controller_name%}
    carregaTabela("#tabela", url + "{%controller_name%}/xhrPesquisar");
    // botão de pesquisa: envia os dados dos campos para o metodo xhrPesquisar
    enviarDadosTabela(".search", ".form-fields", url + "{%controller_name%}/xhrPesquisar", "#tabela");
    // botão refresh: recarregar a tabela de pesquisar com todos os dados
    enviarDadosTabela(".search-refresh", "data", url + "{%controller_name%}/xhrPesquisar", "#tabela");
    // botão imprirmir: envia os dados dos campos para o metodo xhrImprimirPesquisa e sai em PDF.
    enviarDadosPdf("#imprimir", ".form-fields", url + "{%controller_name%}/xhrImprimirPesquisa");

});
