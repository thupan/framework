/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/


$(document).ready(function(d) {

    // ABA1 BEGIN
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
    // ABA1 END

});
