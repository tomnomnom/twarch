<?php
namespace Twarch\Module;

class TimeOfDay extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created FROM tweets
    ');

    $findTweets->execute();

    $times = array();

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $hour = date('H', $tweet->created);
      if (!isset($times[$hour])){
        $times[$hour] = 0;
      }
      $times[$hour]++;
    }
    ksort($times);

    $fields = array('Hour', 'Count');

    $rows = array();
    foreach ($times as $hour => $count){
      $rows[] = array($hour, $count);
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
