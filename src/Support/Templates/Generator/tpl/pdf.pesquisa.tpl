<?php

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
        self::$pdf->SetFont('courier', '', 12);
    }

    public static function conteudo($data = null) {
        self::configure();

        self::$pdf->SetDrawColor(0);
        self::$pdf->SetFillColor(200);
        self::$pdf->SetBackgrounds(true);

        $col = 23.14;
        self::$pdf->SetAligns(['C']);
        self::$pdf->SetWidths([1*$col,
                               3*$col,
                               2*$col,
                               3*$col,
                               3*$col]);

         self::$pdf->SetBorders([0=>'LT',3=>'T']);

        // Titulo
        self::$pdf->newRow([
            {%HeaderClean%}
        ]);

        self::$pdf->SetBackgrounds([true, false,true, false, true]);
        self::$pdf->SetDrawColor(0);
//        unset(self::$pdf->borders);
        self::$pdf->SetBorders(['LB','B','B','B','RB']);
        // Imprimir linhas
        foreach ($data as $linha) {
            self::$pdf->newRow([
                {%FieldsPDF%}
            ]);
        }

        return self::$pdf->Output();
    }

}
