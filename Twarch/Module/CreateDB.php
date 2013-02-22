<?php
namespace Twarch\Module;

class CreateDB extends \Twarch\Module {
  public function exec($args){
    $this->db->query('DROP TABLE IF EXISTS tweets');
    $this->db->query('
      CREATE VIRTUAL TABLE tweets USING fts4(
        id      INTEGER, 
        created INTEGER, 
        text    TEXT
      )
    ');
    
    $this->setResult(new \Twarch\Result\Text(
      "Successfully created DB"
    ));

    return true;
  }
}

