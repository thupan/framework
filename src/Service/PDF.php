<?php

namespace Service;

use \FPDF;

function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}    

class PDF extends FPDF
{
    var $aligns;
    var $backgrounds;
    var $borders;
    var $fills;
    var $fontColor;
    var $fontes; // fonts já é usado
    var $heights;
    var $sizes;
    var $styles;
    var $valigns;
    var $widths;
    var $resetar;

    var $ALIGN='';
    var $angle=0;    
    //variables of html parser
    var $B;
    var $I;
    var $U;
    var $HREF;

    var $fontList;
    var $issetfont;
    var $issetcolor;

    function __construct(){
        parent::__construct();
        $this->B=0;
        $this->I=0;
        $this->U=0;
        $this->HREF='';
        $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont=false;
        $this->issetcolor=false;        
    }

    function WriteHTML($html)
    {
        //HTML parser
        $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(5,stripslashes(txtentities($e)));
            }
            else
            {
                //Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    //Extract attributes
                    $a2=explode(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $attr=array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])]=$a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }
    
    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
                $this->SetStyle('B',true);
                break;
            case 'EM':
                $this->SetStyle('I',true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag,true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }
    
    function CloseTag($tag)
    {
        //Closing tag
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont=false;
            }
        }
    }
    
    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
        {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }
    
    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
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
    function SetFontColor($color){
        //Set the array of colors
        $this->fontColor = $color;
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
            // Array Font Color           
            if(!is_null($this->fontColor)){
                if (!is_array($this->fontColor)) {
                    $cl = is_integer($this->fontColor) ? $this->fontColor : false;
                } else {
                    $cl = isset($this->fontColor[$i]) ? $this->fontColor[$i] : 0;
                }

                if (!is_array($cl)){
                    $this->SetTextColor($cl);
                } else {
                    $this->SetTextColor($cl[0],$cl[1],$cl[2]);
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
            $this->fontColor    = 0;
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
        ($conf->fontColor ) ? $this->SetFontColor  ($conf->fontColor ):false;
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

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
    
    function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        parent::_endpage();
    }    


//Private properties
var $tmpFiles = array(); 

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function Image($file, $x = null, $y = null, $w=0, $h=0, $type='', $link='',  $isMask=false,  $maskImg=0)
{
    //Put an image on the page
    if(!isset($this->images[$file]))
    {
        //First use of image,  get info
        if($type=='')
        {
            $pos=strrpos($file, '.');
            if(!$pos)
                $this->Error('Image file has no extension and no type was specified: '.$file);
            $type=substr($file, $pos+1);
        }
        $type=strtolower($type);
        $mqr=get_magic_quotes_runtime();
        //set_magic_quotes_runtime(0);
        if($type=='jpg' || $type=='jpeg')
            $info=$this->_parsejpg($file);
        elseif($type=='png'){
            $info=$this->_parsepng($file);
            if ($info=='alpha') return $this->ImagePngWithAlpha($file, $x, $y, $w, $h, $link);
        }
        else
        {
            //Allow for additional formats
            $mtd='_parse'.$type;
            if(!method_exists($this, $mtd))
                $this->Error('Unsupported image type: '.$type);
            $info=$this->$mtd($file);
        }
        //set_magic_quotes_runtime($mqr);
        
        if ($isMask){
      $info['cs']="DeviceGray"; // try to force grayscale (instead of indexed)
    }
        $info['i']=count($this->images)+1;
        if ($maskImg>0) $info['masked'] = $maskImg;###
        $this->images[$file]=$info;
    }
    else
        $info=$this->images[$file];
    //Automatic width and height calculation if needed
    if($w==0 && $h==0)
    {
        //Put image at 72 dpi
        $w=$info['w']/$this->k;
        $h=$info['h']/$this->k;
    }
    if($w==0)
        $w=$h*$info['w']/$info['h'];
    if($h==0)
        $h=$w*$info['h']/$info['w'];
    
    // embed hidden,  ouside the canvas
    if ((float)FPDF_VERSION>=1.7){
        if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageSize[0]:$this->CurPageSize[1]) + 10;
    }else{
        if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageFormat[0]:$this->CurPageFormat[1]) + 10;
    }
        
    $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k, $info['i']));
    if($link)
        $this->Link($x, $y, $w, $h, $link);
        
    return $info['i'];
}

// needs GD 2.x extension
// pixel-wise operation,  not very fast
function ImagePngWithAlpha($file, $x, $y, $w=0, $h=0, $link='')
{
    $tmp_alpha = tempnam('.',  'mska');
    $this->tmpFiles[] = $tmp_alpha;
    $tmp_plain = tempnam('.',  'mskp');
    $this->tmpFiles[] = $tmp_plain;
    
    list($wpx,  $hpx) = getimagesize($file);
    $img = imagecreatefrompng($file);
    $alpha_img = imagecreate( $wpx,  $hpx );
    
    // generate gray scale pallete
    for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img,  $c,  $c,  $c);
    
    // extract alpha channel
    $xpx=0;
    while ($xpx<$wpx){
        $ypx = 0;
        while ($ypx<$hpx){
            $color_index = imagecolorat($img,  $xpx,  $ypx);
            $alpha = 255-($color_index>>24)*255/127; // GD alpha component: 7 bit only,  0..127!
            imagesetpixel($alpha_img,  $xpx,  $ypx,  $alpha);
        ++$ypx;
        }
        ++$xpx;
    }

    imagepng($alpha_img,  $tmp_alpha);
    imagedestroy($alpha_img);
    
    // extract image without alpha channel
    $plain_img = imagecreatetruecolor ( $wpx,  $hpx );
    imagecopy ($plain_img,  $img,  0,  0,  0,  0,  $wpx,  $hpx );
    imagepng($plain_img,  $tmp_plain);
    imagedestroy($plain_img);
    
    //first embed mask image (w,  h,  x,  will be ignored)
    $maskImg = $this->Image($tmp_alpha,  0, 0, 0, 0,  'PNG',  '',  true); 
    
    //embed image,  masked with previously embedded mask
    $this->Image($tmp_plain, $x, $y, $w, $h, 'PNG', $link,  false,  $maskImg);
}

function Close()
{
    parent::Close();
    // clean up tmp files
    foreach($this->tmpFiles as $tmp) @unlink($tmp);
}

/*******************************************************************************
*                                                                              *
*                               Private methods                                *
*                                                                              *
*******************************************************************************/
function _putimages()
{
    $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
    reset($this->images);
    foreach($this->images as $file => $info)
    //while(list($file, $info)=each($this->images))
    {
        $this->_newobj();
        $this->images[$file]['n']=$this->n;
        $this->_out('<</Type /XObject');
        $this->_out('/Subtype /Image');
        $this->_out('/Width '.$info['w']);
        $this->_out('/Height '.$info['h']);
        
        if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ###
        
        if($info['cs']=='Indexed')
            $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
        else
        {
            $this->_out('/ColorSpace /'.$info['cs']);
            if($info['cs']=='DeviceCMYK')
                $this->_out('/Decode [1 0 1 0 1 0 1 0]');
        }
        $this->_out('/BitsPerComponent '.$info['bpc']);
        if(isset($info['f']))
            $this->_out('/Filter /'.$info['f']);
        if(isset($info['parms']))
            $this->_out($info['parms']);
        if(isset($info['trns']) && is_array($info['trns']))
        {
            $trns='';
            for($i=0;$i<count($info['trns']);$i++)
                $trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
            $this->_out('/Mask ['.$trns.']');
        }
        $this->_out('/Length '.strlen($info['data']).'>>');
        $this->_putstream($info['data']);
        unset($this->images[$file]['data']);
        $this->_out('endobj');
        //Palette
        if($info['cs']=='Indexed')
        {
            $this->_newobj();
            $pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
            $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
            $this->_putstream($pal);
            $this->_out('endobj');
        }
    }
}

// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels.
// if you only use the ImagePngWithAlpha method for such PNGs,  you can remove it from this script.
function _parsepng($file)
{
    //Extract info from a PNG file
    $f=fopen($file, 'rb');
    if(!$f)
        $this->Error('Can\'t open image file: '.$file);
    //Check signature
    if(fread($f, 8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
        $this->Error('Not a PNG file: '.$file);
    //Read header chunk
    fread($f, 4);
    if(fread($f, 4)!='IHDR')
        $this->Error('Incorrect PNG file: '.$file);
    $w=$this->_readint($f);
    $h=$this->_readint($f);
    $bpc=ord(fread($f, 1));
    if($bpc>8)
        $this->Error('16-bit depth not supported: '.$file);
    $ct=ord(fread($f, 1));
    if($ct==0)
        $colspace='DeviceGray';
    elseif($ct==2)
        $colspace='DeviceRGB';
    elseif($ct==3)
        $colspace='Indexed';
    else {
        fclose($f);      // the only changes are 
        return 'alpha';  // made in those 2 lines
    }
    if(ord(fread($f, 1))!=0)
        $this->Error('Unknown compression method: '.$file);
    if(ord(fread($f, 1))!=0)
        $this->Error('Unknown filter method: '.$file);
    if(ord(fread($f, 1))!=0)
        $this->Error('Interlacing not supported: '.$file);
    fread($f, 4);
    $parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
    //Scan chunks looking for palette,  transparency and image data
    $pal='';
    $trns='';
    $data='';
    do
    {
        $n=$this->_readint($f);
        $type=fread($f, 4);
        if($type=='PLTE')
        {
            //Read palette
            $pal=fread($f, $n);
            fread($f, 4);
        }
        elseif($type=='tRNS')
        {
            //Read transparency info
            $t=fread($f, $n);
            if($ct==0)
                $trns=array(ord(substr($t, 1, 1)));
            elseif($ct==2)
                $trns=array(ord(substr($t, 1, 1)), ord(substr($t, 3, 1)), ord(substr($t, 5, 1)));
            else
            {
                $pos=strpos($t, chr(0));
                if($pos!==false)
                    $trns=array($pos);
            }
            fread($f, 4);
        }
        elseif($type=='IDAT')
        {
            //Read image data block
            $data.=fread($f, $n);
            fread($f, 4);
        }
        elseif($type=='IEND')
            break;
        else
            fread($f, $n+4);
    }
    while($n);
    if($colspace=='Indexed' && empty($pal))
        $this->Error('Missing palette in '.$file);
    fclose($f);
    return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
}

}

