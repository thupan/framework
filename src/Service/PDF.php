<?php

namespace Service;

use \FPDF;

class PDF extends FPDF
{
    var $aligns;
    var $backgrounds;
    var $borders;
    var $fills;
    var $fontes; // fonts já é usado
    var $heights;
    var $sizes;
    var $styles;
    var $valigns;
    var $widths;
    var $resetar;

    function __construct(){
        parent::__construct();
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function SetBackgrounds($bfc) {
        //Set the array of column alignments
        $this->backgrounds = $bfc;
    }

    function SetBorders($b) {
        //Set the array of borders
        $this->borders = $b;
    }

    function SetFills($fl){
        //Set the array of fills
        $this->fills = $fl;
    }

    function SetFonts($f){
        $this->fontes = $f;
    }

    function SetHeights($h){
        $this->heights = $h;
    }

    function SetSizes($si){
        $this->sizes = $si;
    }

    function SetStyles($s){
        $this->styles = $s;
    }

    function SetVAligns($v){
        $this->valigns = $v;
    }

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function ResetConfig($r) {

        $this->resetar = $r;
    }

    function Row($data) {

        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            // Seta a configuração de um array de fontes
            // FONTE
            if (!is_array($this->fontes)) {
                $fonte = is_string($this->fontes) ? $this->fontes : false;
            } else {
                $fonte = isset($this->fontes[$i]) ? $this->fontes[$i] : 'Helvetica';
            }
            // ESTILO
            if (!is_array($this->styles)) {
                $estilo = is_string($this->styles) ? $this->styles : false;
            } else {
                $estilo = isset($this->styles[$i]) ? $this->styles[$i] : '';
            }
            // TAMANHO
            if (!is_array($this->sizes)) {
                $tamanho = is_integer($this->sizes) ? $this->sizes : false;
            } else {
                $tamanho = isset($this->sizes[$i]) ? $this->sizes[$i] : 9;
            }
            if ( isset($this->fontes) || isset($this->styles)  || isset($this->sizes) )
                $this->SetFont($fonte, $estilo, $tamanho);

            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i])); // retorna o maior número de linhas
        }

        $h = ($this->heights ? $this->heights : 5) * $nb;
        //Concatenate with blank for background fill
        for ($i = 0; $i < count($data); $i++) {
            $v = !is_array($this->valigns) ? $this->valigns : $this->valigns[$i];
            $diff = $nb - $this->NbLines($this->widths[$i], $data[$i]); // diferença entre a maior linha e a linha atual
            if ($diff > 0) {
                for ($j = 0; $j < ($diff); $j++){
                    if ($v != 'M' && $v != 'B')
                        $data[$i].="\n ";
                    if ($v == 'M' && $j < ($diff/2))
                        $data[$i] = "\n".$data[$i];
                    if ($v == 'M' && $j >= ($diff/2))
                       $data[$i] .="\n ";
                    if ($v == 'B')
                       $data[$i] = "\n ".$data[$i];
                }
            }
        }
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            if (!is_array($this->aligns)) {
                $a = is_string($this->aligns) ? $this->aligns : false;
            } else {
                $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            }

            // Array Backgrounds
            if (!is_array($this->backgrounds)) {
                $bfc = $this->backgrounds == true ? true : false;
            } else {
                $bfc = isset($this->backgrounds[$i]) ? $this->backgrounds[$i] : false;
            }

            // Array Fill Color
            if(!is_null($this->fills)){
                if (!is_array($this->fills)) {
                    $fi = is_integer($this->fills) ? $this->fills : false;
                } else {
                    $fi = isset($this->fills[$i]) ? $this->fills[$i] : 255;
                }

                if (!is_array($fi)){
                    $this->SetFillColor($fi);
                } else {
                    $this->SetFillColor($fi[0],$fi[1],$fi[2]);
                }
            }

            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            if (is_null($this->borders) && $this->borders!=0) {
                $this->Rect($x, $y, $w, $h);
            }
            // Array para setar bordas independentes
            if (!is_array($this->borders)) {
                $b = $this->borders;

            } else {
                $b = isset($this->borders[$i]) ? $this->borders[$i] : 0;
            }
            //Seta Fontes, Tamanhos e Formatos independentes
            // FONTE
            if (!is_array($this->fontes)) {
                $fonte = is_string($this->fontes) ? $this->fontes : false;
            } else {
                $fonte = isset($this->fontes[$i]) ? $this->fontes[$i] : 'Helvetica';
            }
            // ESTILO
            if (!is_array($this->styles)) {
                $estilo = is_string($this->styles) ? $this->styles : false;
            } else {
                $estilo = isset($this->styles[$i]) ? $this->styles[$i] : '';
            }
            // TAMANHO
            if (!is_array($this->sizes)) {
                $tamanho = is_integer($this->sizes) ? $this->sizes : false;
            } else {
                $tamanho = isset($this->sizes[$i]) ? $this->sizes[$i] : 9;
            }
            if ( isset($this->fontes) || isset($this->styles)  || isset($this->sizes))
                $this->SetFont($fonte, $estilo, $tamanho);

            //Print the text
            $this->AutoPageBreak = false;
            $this->MultiCell($w, ($this->heights ? $this->heights : 5), $data[$i], $b, $a, $bfc);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }

        if ($this->resetar){
            $this->valigns      = null;
            $this->aligns       = null;
            $this->backgrounds  = null;
            $this->borders      = null;
            $this->fills        = null;
            $this->fontes       = null;
            $this->heights      = null;
            $this->sizes        = null;
            $this->styles       = null;
            $this->widths       = null;
            $this->resetar      = null;
        }

        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    /**
         * O formato do array segue nas especificações abaixo:
         * As chaves possíveis são:

         * CHAVE        | OPÇÕES                                    | DESCRIÇÃO

         * border       : ('B' &| 'T' &| 'R' &| 'L') || 1 || 0      - string ou inteiro (bordas da celula)
         * style        : 'B' | 'I' | 'U' | ''                      - string (formato da fonte)
         * font         : 'NomeDaFonte'                             - string (nome da fonte)
         * background   : true || false                             - boolean (com fundo ou sem fundo)
         * fill         : 0 à 255                                   - inteiro (cor do preenchimento)
         * size         : 4 à 62                                    - inteiro (tamanho da fonte)
         * align        : 'L' || 'R' || 'C' || 'J'                  - string (alinhamento do texto)
         * width        : 1 a (275<landscape> || 190<portrait>)     - array (tamanho da coluna)
         * height       : 1 a N                                     - inteiro (tamanho da altura da linha)
         * reset        : true || false                             - boolean (reseta as configurações do row)
         *
         * OBS: para cada CHAVE pode-se receber UM VALOR ou um ARRAY com valores
         *
         * @param mixed[] $config Array de configurações com chave e valores.
         *
         * Exemplo para colunas padronizadas (como um theader):

         $arrayConfig = [
            'border'        => 1,                // aceita array também
            'style'         => 'B',              // aceita array também
            'font'          => 'Helvetica',      // aceita array também
            'background'    => true,             // aceita array também
            'fill'          => 240,              // aceita array também
            'size'          => 9,                // aceita array também
            'align'         => 'C',              // aceita array também
            'width'         => [15,40,23,20,20], // só aceita array
            'height'        => 5 ,               // só aceita inteiro
            'reset'        => true               // só aceita booleano
        ];

         */

    public function RowConfig($config){

        $conf = (object) $config;

        ($conf->align     ) ? $this->SetAligns     ($conf->align     ):false;
        ($conf->background) ? $this->SetBackgrounds($conf->background):false;
        ($conf->border    ) ? $this->SetBorders    ($conf->border    ):false;
        ($conf->fill      ) ? $this->SetFills      ($conf->fill      ):false;
        ($conf->font      ) ? $this->SetFonts      ($conf->font      ):false;
        ($conf->height    ) ? $this->SetHeights    ($conf->height    ):false;
        ($conf->style     ) ? $this->SetStyles     ($conf->style     ):false;
        ($conf->size      ) ? $this->SetSizes      ($conf->size      ):false;
        ($conf->valign    ) ? $this->SetVAligns    ($conf->valign    ):false;
        ($conf->width     ) ? $this->SetWidths     ($conf->width     ):false;
        ($conf->reset     ) ? $this->ResetConfig   ($conf->reset     ):false;

    }

    // Funções adicionais para calcular intervalos

    // Function for sum intervals of an array
    function array_sum_interval($array_external, $interval){

        $n_interval=explode(":", $interval);
        $sum = 0;
        for($i=$n_interval[0]; $i < ($n_interval[1]+1) ; $i++){
            $sum += $array_external[$i];
        }
        return $sum;

    }
    // Function for sum a interval of an array
    function array_sum_intervals($array_external, $intervals){

        $n_intervals=explode(";", $intervals);
        $array_amount = count($n_intervals);
        $result = 0;
        for($i=0; $i < $array_amount ; $i++){
            $result += $this->array_sum_interval($array_external, $n_intervals[$i]);
        }
        return $result;

    }

    // Matriz Transposta - transforma array dados de linhas em colunas
    function flipDiagonally($arr)
    {
        $out = array();
        foreach ($arr as $key => $subarr) {
            foreach ($subarr as $subkey => $subvalue) {
                $out[$subkey][$key] = $subvalue;
            }
        }
        return $out;
    }



}
