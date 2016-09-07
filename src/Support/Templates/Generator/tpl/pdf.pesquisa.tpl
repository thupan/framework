<?php
/************************************************************
 * SCRIPT CRIADO PELO GERADOR DE CÓDIGO v{%GC_VERSION%}
 * CRIADO EM: {%GC_DATE%}
 * GERADO POR: {%GC_DEVELOPER%} @ {%GC_MACHINE%}
 ************************************************************/
 
namespace App\Pdf;

use \App\Helpers\PDF;
use \Exception;

class Pdf{%Controller%}Pesquisa {

    protected static $pdf;

    public static function configure(){
        self::$pdf = new PDF('L');
        self::$pdf->setTitle('Lista de {%Controller%} - Pesquisa');
        self::$pdf->AliasNbPages();
        self::$pdf->AddPage();
        self::$pdf->SetFont('Helvetica', '', 12);
    }

    public static function conteudo($data = null) {
        self::configure();

        self::$pdf->SetDrawColor(0);
        self::$pdf->SetFillColor(200);
        self::$pdf->SetBackgrounds(true);

        $col = 23.14;

        self::$pdf->SetWidths([1*$col,
                               3*$col,
                               2*$col,
                               3*$col,
                               3*$col]);

         self::$pdf->SetAligns(['C', 'C', 'C', 'C', 'C']);

        // Titulo
        self::$pdf->Row([
            {%HeaderClean%}
        ]);

        self::$pdf->SetBackgrounds(false);
        self::$pdf->SetDrawColor(0);
        self::$pdf->SetAligns(['C', 'L', 'L', 'L', 'L']);

        // Imprimir linhas
        foreach ($data as $linha) {
            self::$pdf->Row([
                {%FieldsPDF%}
            ]);
        }

        return self::$pdf->Output();
    }
}
