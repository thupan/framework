/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/


$(document).ready(function(d) {

    // Carregar o HgridView
    HgridView("#Htabela", url + "{%controller_name%}/xhrPesquisar");
    
    // botão imprirmir: envia os dados dos campos para o metodo xhrImprimirPesquisa e sai em PDF.
    enviarDadosPdf("#imprimir", ".form-fields", url + "{%controller_name%}/xhrImprimirPesquisa");

});
