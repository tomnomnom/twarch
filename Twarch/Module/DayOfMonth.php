<?php
namespace Twarch\Module;

class DayOfMonth extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created FROM tweets
    ');

    $findTweets->execute();

    $days = array();

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $day = date('d', $tweet->created);
      if (!isset($days[$day])){
        $days[$day] = 0;
      }
      $days[$day]++;
    }

    ksort($days);

    $fields = array('Day', 'Count');

    $rows = array();
    foreach ($days as $day => $count){
      $rows[] = array($day, $count);
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
