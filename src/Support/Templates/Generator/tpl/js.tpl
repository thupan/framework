
$(document).ready(function() {
    // funções globais da tela.
    // inserir aqui

    // funções que serão executados no método principal.
    if(method === 'index') {
        carregaTabela("#tabela", url + "{%controller_name%}/xhrPesquisar");
        enviarDadosTabela(".search", ".form-fields", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        enviarDadosTabela(".search-refresh", "data", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        enviarDadosPdf("#imprimir", ".form-fields", url + "{%controller_name%}/xhrImprimirPesquisa");
    }
});
