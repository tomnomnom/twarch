<?php
namespace Twarch;

class Factory {

  public function db(){
    $db = new \PDO('sqlite:'.__DIR__.'/../tweets.sq3');
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $db;
  }

  public function statsStorage(){
    return new StatsStorage($this->db());
  }
}
