<?php
namespace Twarch\Result;

class Text extends \Twarch\Result {
  protected $text;
  public function __construct($text){
    $this->text = $text;
  }

  public function get(){
    return $this->text;
  }
}

