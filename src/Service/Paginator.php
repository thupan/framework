<?php
/**
 * Classe controladora de paginação de dados em array.
 *
 * @package \Services\Paginator
 * @version 1.0
 *
 */

namespace Service;

class Paginator {
  public  $current_page;
  public  $total_page;
  public  $links_page;
  public $id;

  public function __construct() {
    $this->links_page = 10;
    $this->per_page = 15;
  }

  protected function array_sort($records, $field, $reverse=false) {
    $hash = [];
    foreach($records as $record) {
       $hash[$record[$field]] = $record;
     }
     ($reverse) ? krsort($hash) : ksort($hash);
     $records = [];
     foreach($hash as $record) {
       $records[] = $record;
     }
     return $records;
   }

  public function paginate(&$data) {
    if(!is_null($data)) {
      $this->current_page = ($_REQUEST['p']) ? $_REQUEST['p'] : 1;

      $page_data = array_chunk($data, $this->per_page);
      $this->total_page = count($page_data);
      $new_data = $page_data[$this->current_page-1];
      $data = $new_data;
      return $this;
    } else {
      return $data;
    }
  }

  private function previous_page() {
    if($this->current_page < 1) {
      return 1;
    } else if($this->current_page <= $this->links_page) {
      return 1;
    } else {
      return $this->current_page - $this->links_page;
    }
  }
  private function next_page() {
    if($this->current_page < $this->total_page) {
      return $this->current_page + $this->links_page;
    } else if($this->current_page > $this->total_page) {
      return $this->total_page;
    } else {
      return $this->current_page;
    }
  }

  public function pages() {
     $id = ($this->id) ? 'pagination-'.$this->id : false;
    $pages = "<div class='text-center'><ul class='pagination $id'>
    <li><a href='?p=1'>
    <span class='glyphicon glyphicon-triangle-left' aria-hidden='true'></span>
    </a></li>
    <li><a href='?p={$this->previous_page()}'>
    <span class='glyphicon glyphicon-menu-left' aria-hidden='true'></span>
    </a></li>";

    if( ($this->current_page - ($this->links_page - 1)) < 1) {
      $previous = 1;
      $next = $this->links_page;
    } else {
      $previous = $this->current_page;
      if( ($this->current_page+$this->links_page) > $this->total_page ) {
        $next = $this->total_page;
      } else {
        $next = $this->current_page + $this->links_page;
      }
    }

    for($i = $previous; $i <= $next; $i++) {
      if($i > $this->total_page) break;

      if($i == $this->current_page) {
        $pages .= "<li class='active'><a href='?p=$i'>$i</a></li>";
      } else {
        $pages .= "<li><a href='?p=$i'>$i</a></li>";
      }
    }
    $pages .= "
    <li><a href='?p={$this->next_page()}'>
    <span class='glyphicon glyphicon-menu-right' aria-hidden='true'></span>
    </a></li>
    <li><a href='?p={$this->total_page}'>
    <span class='glyphicon glyphicon-triangle-right' aria-hidden='true'></span>
    </a></li>
    </ul>
    <p class='text-center'><i>$this->current_page de <b>$this->total_page</b></i></p>
    </div>";

    return ($this->total_page) ? $pages : false;
  }
}
