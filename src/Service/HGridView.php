<?php

/**
 * Classe de HGridView.
 * Para usar é só declarar use \Service\HGridView;
 * @package \Service\HGridView
 * @version 1.0.0
 *
 *
 **/

namespace Service;
// Dependências
use Service\Paginator;
use Service\Session;
use Service\Record;
use Routing\Router;

class HGridView 
{            
    private static $paginate = true;
    private static $action = true;
    private static $optionActions = [];
    private static $onPageSize = false;
    private static $pageSize = 10;
    private static $id = ROUTER_REQUEST;
    private static $reload = false;
    private static $search =true;
    private static $controller ='xhrPesquisar';
    private static $pageRows =0;
    private static $totalReg =0;
    private static $gridOrder = true;
    private static $orderColField = '';
    private static $onOrder = true;
    
    public static function open($option=[])
    {
        array_key_exists('id',$option)? self::$id = 'tabela-'.$option['id']:self::$id = 'tabela-'.md5(self::$id);
        array_key_exists('reload',$option)? self::$reload = $option['reload']:false;
        array_key_exists('search',$option)? self::$search = $option['search']:false;
        array_key_exists('onPageSize',$option)? self::$onPageSize = $option['onPageSize']:false;
        array_key_exists('onOrder',$option)? self::$onOrder = $option['onOrder']:false;
      
        self::$controller = ROUTER_REQUEST;

        if(!self::$reload){
            // $html = Html::beginTag('div',[]);
            // $html.= 'Registro(s): '.Html::tag('strong','{pageRow} - {totalReg}',['id'=>'regsum-'.self::$id]);
            // $html.= Html::endTag('div');
            if(self::$onPageSize){
                $html.= Html::beginTag('div',['id'=>'lines-'.self::$id]);
                $html.= ' <p><b>Linhas por página(s):</b></p>'.Html::tag('div','{pageSize}',['class'=>'btn-group linkPage-'.self::$id, 'role'=>'group', 'style'=>'margin-bottom:10px;']);
                $html.= Html::beginTag('form',['class'=>'form-LinkPage-'.self::$id]);
                $html.= Html::input('hidden','inputLinkPage-'.self::$id,self::$pageSize,['id'=>'inputLinkPage-'.self::$id]);
                $html.= Html::endTag('form');
                $html.= Html::endTag('div');
            }
            $html.= Html::beginTag('table',['class'=>'table table-striped table-hover listview']);
            $html.= self::header($option);
            $html.= Html::beginTag('tbody',['id'=>self::$id]);
        }
       
        $html.= self::rows($option);

if(!self::$reload){
        $html.= Html::tag('script',"                 
// ========== HgridView function ===========
$('.form-control-select2').select2({
        placeholder: 'Escolha...',
        allowClear: true,
});

//$('#regsum-".self::$id."').html('".self::$pageRows." de ".self::$totalReg."');

$('.linkPage-".self::$id."').html('".self::linkPage()."');

$(document).on('keypress','.search-field-".self::$id."',function(event){
    if ( event.which == 13 ) { 
        event.preventDefault();
        HgridViewSearchField('#'+$('.search-".self::$id."').attr('rel'),'.search-field-".self::$id."',url + '".self::$controller."');
        return false;
    }                        
});
$(document).on('click','.search-".self::$id."',function(){       
    HgridViewSearchField('#'+$(this).attr('rel'),'.search-field-".self::$id."',url + '".self::$controller."');   
    return false;    
});                    
$(document).on('click','.search-refresh-".self::$id."',function(){        
    var fields = $(document).find('.search-field-".self::$id."').serializeArray();
    HgridView('#'+$(this).attr('rel'), url + '".self::$controller."',fields);
    return false;    
});
$(document).on('click','.pagination-".self::$id." li a', function(e) {
    e.preventDefault();
    var fields = $(document).find('.search-field-".self::$id."').serializeArray();
    var page = $(this).attr('href');
    HgridView('#".self::$id."',url +'".self::$controller."'+page,fields);
    return false;
});
$(document).on('click','.linkPage-".self::$id." a', function(e) {
    e.preventDefault();
    var fieldsline = null;
    var fields = null;
    $('#inputLinkPage-".self::$id."').val($(this).attr('href'));

    fields = $(document).find('.search-field-".self::$id."').serializeArray();
    fieldsline = fields.concat($(document).find('.form-LinkPage-".self::$id."').serializeArray());
    console.log(fieldsline);
    HgridView('#".self::$id."',url +'".self::$controller."',fieldsline);
    return false;
});
$(document).on('click','.gridOrder-".self::$id."', function(e) {
     if($(this).attr('rel')=='SORT_ASC'){
         $(this).attr('rel','SORT_DESC');
     }else{
         $(this).attr('rel','SORT_ASC');
     }

     SORT_GRID = {name:'gridOrder-".self::$id."', value:$(this).attr('name')+'&'+$(this).attr('rel')};

     fields = $(document).find('.search-field-".self::$id."').serializeArray();
     fields = fields.concat(SORT_GRID);
     fieldsline = fields.concat($(document).find('.form-LinkPage-".self::$id."').serializeArray());
     
     $('#".self::$id."').load(url +'".self::$controller."',fieldsline);
  
    return false;
});

");
        
        
            $html.= Html::endTag('table');            
        }
        echo $html;
    }

    private static function header($option='')
    {
       $url = URL . \Routing\Router::getControllerName();
       
       $colums  = $option['colums'];  
       
       $data = $option['dados'];

       $html = Html::beginTag('thead',[]);
       // Label
       $html .= Html::beginTag('tr',[]);
       foreach($colums as $key =>$value)
       {
           if(self::$onOrder){
               //Label com order
               if(!is_array($value)){
                $html .= Html::tag('th',Html::a(str_replace('_',' ',$value),$url,['class'=>'gridOrder-'.self::$id,'rel'=>'SORT_ASC', 'name'=>$value]),[]);
               }else{

                   if(is_callable($value['label'])){

                       $html .= Html::tag('th',$value['label']($url),[]);
                   }
                   else
                   {
                       if(!is_array($value['label'])){
                            $html .= Html::tag('th',Html::a($value['label'],$url,['class'=>'gridOrder-'.self::$id,'rel'=>'SORT_ASC', 'name'=>$value['searchField']]),[]);
                       }else{
                            $l = $value['label'];
                            array_key_exists('option',$l) ? $op = $l['option']:$op=[];
                            $html .= Html::tag('th',Html::a($l['value'],$url,['class'=>'gridOrder-'.self::$id,'rel'=>'SORT_ASC', 'name'=>$l['name']]),$op);
                       }
                   }
               }
           }else{
               //Label sem order
               if(!is_array($value)){
                $html .= Html::tag('th',str_replace('_',' ',$value),[]);
               }else{

                   if(is_callable($value['label'])){

                       $html .= Html::tag('th',$value['label']($url),[]);
                   }
                   else
                   {
                       if(!is_array($value['label'])){
                            $html .= Html::tag('th',$value['label'],[]);
                       }else{
                            $l = $value['label'];
                            array_key_exists('option',$l) ? $op = $l['option']:$op=[];
                            $html .= Html::tag('th',$l['value'],$op);
                       }
                   }
               }
           }
       }
       $html .= Html::endTag('tr');
       
       if(self::$search){
       // search-fields
       $html .= Html::beginTag('tr',['class'=>'search-fields']);       
       $html .= Html::beginTag('form',['class'=>'form-fields"']);

       foreach($colums as $key =>$value)
       {
           if(!is_array($value)){
               $html .= Html::beginTag('td',[]);
               $html .= Html::input('text',$value.':ANY','',['class'=>"form-control search-field-".self::$id]);
               $html .= Html::endTag('td');
           }else{
               if(is_callable($value['searchField'])){
                  if($value['searchField']($url)){
                   $tag = Html::tag('td',$value['searchField']($url));
                   $tag = str_replace('search-field','search-field-'.self::$id,$tag);
                   $html .= $tag;
                  }
               }else{
                 if(!is_array($value['searchField'])){
                       $html .= Html::beginTag('td',[]);
                       $html .= Html::input('text',$value['searchField'],'',['class'=>"form-control search-field-".self::$id]);
                       $html .= Html::endTag('td');
                   }else{
                        $l = $value['searchField'];
                        array_key_exists('option',$l) ? $op = $l['option']:$op=[];
                        $html .= Html::beginTag('td',$op);
                        $html .= Html::input('text',$l['name'],'',['class'=>"form-control search-field-".self::$id]);
                        $html .= Html::endTag('td');
                   }
               }
           }
       }

       // search-button
       $html .= Html::beginTag('td',[]);
       $html .= Html::beginTag('div',['class'=>"btn-group", 'role'=>"group"]);
       $html .= Html::button(Html::tag('span','',['class'=>"glyphicon glyphicon-search", 'aria-hidden'=>"true"]),['class'=>"btn btn-primary search-".self::$id, 'rel'=>self::$id, 'alt'=>"PESQUISAR", 'title'=>"PESQUISAR"]);
       $html .= Html::button(Html::tag('span','',['class'=>"glyphicon glyphicon-refresh", 'aria-hidden'=>"true"]),['class'=>"btn btn-default search-refresh-".self::$id, 'rel'=>self::$id, 'alt'=>"RECARREGAR TABELA", 'title'=>"RECARREGAR TABELA"]);
       $html .= Html::endTag('div');
       $html .= Html::endTag('td');
       
       $html .= Html::endTag('form');
       $html .= Html::endTag('tr');
       }
       $html .= Html::endTag('thead');      

       return $html; 
    }

    public static function rows($option=[])
    {   
       
        if(self::$orderColField!=''){
            $data = self::orderCol($option['dados'], self::$orderColField, self::$gridOrder);
           
        }else{
            $data = $option['dados'];
        }

        $url = URL . \Routing\Router::getControllerName();

        // validação de chaves
        array_key_exists('action',$option)? self::$action = $option['action']:false;
        array_key_exists('optionActions',$option)? self::$optionActions = $option['optionActions']:false;
        array_key_exists('pageSize',$option)? self::$pageSize = $option['pageSize']:false;
        array_key_exists('paginate',$option)? self::$paginate = $option['paginate']:false;
        
        // se houver registros
        if($data) {

            self::$totalReg = count($data);
            
            $colums  = $option['colums'];  

            $countRow = 0;

            //prepara os dados para paginação, se solicitado.
            if(self::$paginate) {
                $paginator = new Paginator();
                $paginator->per_page = self::$pageSize;
                $paginator->paginate($data);                               
            }
            
            foreach($data as $key=>$value){
                $html .= Html::beginTag('tr',[]);
                foreach($colums as $k=>$c){
                        if(!is_array($c)){
                            $html .= Html::tag('td',$value[$c]);
                        }
                        else
                        {
                            array_key_exists('option',$c) ? $op = $c['option']:$op=[];

                            if(is_callable($c['value'])){
                                
                                $html .= Html::tag('td',$c['value']($value,$url),$op);
                            }
                            else
                            {
                                $html .= Html::tag('td',$value[$c['value']],$op);
                            }
                        }   
                }
                
                $html.= (self::$action)? Html::tag('td',self::actions($option,$value),self::$optionActions):Html::tag('td');
                $html.= Html::endTag('tr');
                $countRow++;
            }
            
            self::$pageRows = $countRow;

            if(is_callable($option['row']['last'])){
            
                $html .= $option['row']['last']($data);
        
            }

            if(self::$paginate) {
                $paginator->id = self::$id;
                $html .= Html::tag('tr',Html::tag('td',$paginator->pages(),['colspan'=>100,'class'=>'nohover']),['class'=>'tfoot']); 
            }

            return $html;
        } else {
            return '<tr class="tfoot">
                        <td class="nohover" colspan="100%" style="padding:30px; font-size:16px;">
                            <i class="fa fa-exclamation-circle"></i> Nenhum registro foi encontrado.
                        </td>
                    </tr>';
        }
    }
       
    public static function request(&$data)
    {        
              
        if($data['inputLinkPage-tabela-'.md5(self::$id)]){
            self::$pageSize = $data['inputLinkPage-tabela-'.md5(self::$id)];
            unset($data['inputLinkPage-tabela-'.md5(self::$id)]);
        }
        if($data['gridOrder-tabela-'.md5(self::$id)]){
            $sort = explode('&',$data['gridOrder-tabela-'.md5(self::$id)]);            
            
            if($sort[1]=='SORT_ASC'){
                self::$gridOrder = true;                
            }else{
                self::$gridOrder = false;      
            }
            $sort[0] = str_replace([':ANY',':FIRST',':LAST',':THIS'],'',$sort[0]);
            $sort[0] = explode('|',$sort[0]);

            if(count($sort[0])>1){
                self::$orderColField=$sort[0][1];
            }else{
                self::$orderColField=$sort[0][0];
            }
            unset($data['gridOrder-tabela-'.md5(self::$id)]);
        }
        
        return  $data;
    }

    public static function actionTemplete($option=null,$value=null){
            
            $link = URL . \Routing\Router::getControllerName();
            // usando a primenira key
            $colums = array_keys($value); 
            $pk = $value[$colums[0]];

            // Template de button padão
            $html = Html::beginTag('div',['class'=>'btn-group', 'role'=>'group']);
            $html.= $option['colActions'];
            $html.= Html::endTag('div');

            $html = str_replace('{detail}',Html::a(Html::tag('i','',['class'=>'glyphicon glyphicon-list']).' '.translate('app','details'),$link.'/detail/'.$pk,['class'=>"btn btn-primary"]),$html);
            $html = str_replace('{edit}',Html::a(Html::tag('i','',['class'=>'glyphicon glyphicon-edit']).' '.translate('app','edit'),$link.'/edit/'.$pk,['class'=>"btn btn-warning"]),$html);
            $html = str_replace('{delete}',Html::a(Html::tag('i','',['class'=>'glyphicon glyphicon-trash']).' '.translate('app','delete'),$link.'/delete/'.$pk,['class'=>"btn btn-danger"]),$html);  
        
        return $html;
    }

    private static function orderCol($records, $field,$order=true)
    {        
       
        $data = Record::sortRecord($records,$field,$order);
        
        return $data;
    }
    
    private static function linkPage()
    {
        $p = 0;
        while($p < 50){
            $p+=10;
            $html.= ' '.Html::a($p,$p,['class'=>"btn btn-default"]);
        }        
        return $html;
    }

    private static function actions($option=null,$value=null)
    {

        $link = URL . \Routing\Router::getControllerName();

        if(is_callable($option['colActions'])){
            
            $html = $option['colActions']($value,$link);
        
        }else{                        
            $html = self::actionTemplete($option,$value);
        }

        return $html;
    }
}
