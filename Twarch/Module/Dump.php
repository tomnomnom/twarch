<?php
namespace Twarch\Module;

class Dump extends \Twarch\Module {
  public function exec($args){
  

    $findTweets = $this->db->prepare('
      SELECT id, created, text FROM tweets ORDER BY created ASC
    ');

    $findTweets->execute(array());

    $fields = array('Id', 'Created', 'Text');

    $rows = array();
    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $rows[] = array(
        $tweet->id,
        date(DATE_ISO8601, $tweet->created),
        str_replace("\n", " ", $tweet->text)
      );
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
