<?php
namespace Twarch\Module;

class Trend extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created FROM tweets
    ');

    $findTweets->execute();

    $args->expectParam('--resolution');
    $resolution = 'year';
    if ($args->paramIsSet('--resolution')){
      $resolution = $args->getParamValue('--resolution');
    }
    if ($resolution != 'day' && $resolution != 'month' && $resolution != 'year'){
      $resolution = 'year';
    }
    
    $dateFormats = array(
      'day'   => 'Y-m-d',
      'month' => 'Y-m',
      'year'  => 'Y'
    );

    $times = array();
    $firstTweet = null;
    $lastTweet = null;
    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      // Track the first and last Tweets
      if (is_null($firstTweet) && is_null($lastTweet)){
        $firstTweet = $tweet;
        $lastTweet = $tweet;
      }
      if ($tweet->created < $firstTweet->created){
        $firstTweet = $tweet;
      }
      if ($tweet->created > $lastTweet->created){
        $lastTweet = $tweet;
      }

      $key = date($dateFormats[$resolution], $tweet->created);
      if (!isset($times[$key])){
        $times[$key] = 0;
      }
      $times[$key]++;
    }

    // Fill in the blank keys
    $start = new \DateTime();
    $start->setTimestamp($firstTweet->created);

    $end = new \DateTime();
    $end->setTimestamp($lastTweet->created);

    $dateIntervals = array(
      'day'   => 'P1D',
      'month' => 'P1M',
      'year'  => 'P1Y'
    );
    $interval = new \DateInterval($dateIntervals[$resolution]);
    
    $period = new \DatePeriod($start, $interval, $end);
    foreach ($period as $date){
      $key = $date->format($dateFormats[$resolution]);
      if (isset($times[$key])) continue;
      $times[$key] = 0;
    }

    ksort($times);

    $fields = array(ucFirst($resolution), 'Count');

    $rows = array();
    foreach ($times as $time => $count){
      $rows[] = array($time, $count);
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
