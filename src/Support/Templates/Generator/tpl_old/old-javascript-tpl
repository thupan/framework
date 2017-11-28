/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/

$(document).ready(function() {
    // funções globais da tela.
    //

    // funções que serão executados no método principal.
    if(method === 'index') {

        // chama o metodo xhrPesquisar do controlador {%controller_name%}
        carregaTabela("#tabela", url + "{%controller_name%}/xhrPesquisar");
        // botão de pesquisa: envia os dados dos campos para o metodo xhrPesquisar
        enviarDadosTabela(".search", ".form-fields", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        // botão refresh: recarregar a tabela de pesquisar com todos os dados
        enviarDadosTabela(".search-refresh", "data", url + "{%controller_name%}/xhrPesquisar", "#tabela");
        // botão imprirmir: envia os dados dos campos para o metodo xhrImprimirPesquisa e sai em PDF.
        enviarDadosPdf("#imprimir", ".form-fields", url + "{%controller_name%}/xhrImprimirPesquisa");

    }

    if(method === 'detail') {

        // Modelo das abas de detail.
        carregaTabela("#tabela-aba1", url + "{%controller_name%}/xhrPesquisarAba1", {
            //CAMPO_ABA1: $("#CAMPO_ABA1").val()
        });

        enviarDadosTabela(".search-aba1", ".form-fields-aba1", url + "{%controller_name%}/xhrPesquisarAba1", "#tabela-aba1");

        enviarDadosTabela(".search-aba1-refresh", "data", url + "{%controller_name%}/xhrPesquisarAba1", "#tabela-aba1", {
            //CAMPO_ABA1: $("#CAMPO_ABA1").val()
        });

        enviarDadosTabela("#form-aba1-add", ".form-aba1", url + "{%controller_name%}/xhrSaveAba1", "#tabela-aba1", {}, function(data) {
            $("#form-aba1-grid").slideUp('slow');
            //limpaCamposAba1();
        });

        enviarDadosPdf("#imprimir-aba1", ".form-fields-aba1", url + "{%controller_name%}/xhrImprimirAba1");

        btnNew('aba1');
        btnCancel('aba1');
        btnDelete('aba1');

        /* Se houver botão de edição:

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

    if(method === ('novo' || 'edit')) {

    }

    if(method === 'edit')   {

    }

    if(method === 'novo')   {

    }
});
