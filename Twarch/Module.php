<?php
namespace Twarch;

abstract class Module {
  protected $db;
  protected $progress;
  protected $result = null;
  protected $failureReason = '';

  public function __construct(\PDO $db, \Phargs\Io\Screen $progress = null){
    $this->db = $db;
    $this->progress = $progress;
  }

  abstract public function exec($args);
    
  protected function progress($message){
    if (!is_null($this->progress)){
      $this->progress->errln($message);
    }
  }

  protected function setResult(Result $result){
    $this->result = $result;
  }

  public function getResult(){
    return $this->result;
  }

  protected function setFailureReason($reason){
    $this->failureReason = $reason;
  }

  public function getFailureReason(){
    return $this->failureReason;
  }
}
