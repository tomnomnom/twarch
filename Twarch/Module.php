<?php
namespace Twarch;

abstract class Module {
  protected $db;
  protected $result = null;

  public function __construct(\PDO $db){
    $this->db = $db;
  }

  abstract public function exec($args);

  protected function setResult(Result $result){
    $this->result = $result;
  }

  public function getResult(){
    return $this->result;
  }
}
