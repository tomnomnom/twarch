<?php
namespace Twarch\Module;

class DayOfWeek extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created FROM tweets
    ');

    $findTweets->execute();

    $days = array(
      'Mon' => 0, 
      'Tue' => 0, 
      'Wed' => 0, 
      'Thu' => 0, 
      'Fri' => 0, 
      'Sat' => 0, 
      'Sun' => 0
    );

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $day = date('D', $tweet->created);
      $days[$day]++;
    }

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
