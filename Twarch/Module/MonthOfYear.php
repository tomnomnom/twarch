<?php
namespace Twarch\Module;

class MonthOfYear extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created FROM tweets
    ');

    $findTweets->execute();

    $days = array(
      'Jan' => 0,
      'Feb' => 0,
      'Mar' => 0,
      'Apr' => 0,
      'May' => 0,
      'Jun' => 0,
      'Jul' => 0,
      'Aug' => 0,
      'Sep' => 0,
      'Oct' => 0,
      'Nov' => 0,
      'Dec' => 0
    );

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $day = date('M', $tweet->created);
      $days[$day]++;
    }

    $fields = array('Month', 'Count');

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
