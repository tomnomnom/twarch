<?php
namespace Twarch\Result;

class Table extends \Twarch\Result {
  protected $fields;
  protected $rows;

  public function __construct(Array $fields, Array $rows){
    $this->fields = $fields;
    $this->rows = $rows;
  }

  public function getFields(){
    return $this->fields;
  }

  public function getRows(){
    return $this->rows;
  }
}
