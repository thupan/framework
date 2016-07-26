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

    if(method === 'detail') {

        btnNew('aba1');
        btnCancel('aba1');
        btnDelete('aba1');

        /*
            exemplo de botao de edicao:
                nome da aba,
                url de acao,
                campos para o controlador,
                campos que nao podem ser apagados do formulario,
                callback(r)

            btnEdit('aba1', url + 'controlador/xhrEditAba1', ['ID_ABA1'], ['CAMPO_ABA1'], function(r) {
            $("#CAMPO_ABA1").val(r[0].CAMPO_ABA1);
        });
        */
    }
    if(method === 'edit')   { }
    if(method === 'novo')   { }
});
