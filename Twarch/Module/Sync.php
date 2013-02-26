<?php
namespace Twarch\Module;

class Sync extends \Twarch\Module {
  protected $fetchAmount = 200;

  public function exec($args){

    $username = $args->getResidualArg(1);

    if (!$username){
      $this->setFailureReason("Please specify a username");
      return false;
    }

    $params = array(
      "screen_name" => $username,
      "include_rts" => "true",
      "count"       => $this->fetchAmount
    );
    
    // Get the ID of the last tweet
    $lastTweet = $this->db->prepare('
      SELECT id, created FROM tweets ORDER BY created DESC LIMIT 1
    ');
    $lastTweet->execute();
    $tweet = $lastTweet->fetch(\PDO::FETCH_OBJ);

    $this->progress("Last Tweet had ID [{$tweet->id}]");
    $params['since_id'] = $tweet->id;

    $total = 0;
    do {
      $r = $this->fetch($params);
      if (!$r || !is_array($params)){
        break;
      }

      $count = $r['count'];
      $total += $count;
      $params['max_id'] = $r['earliestTweet']->id - 1;
    } while($count != 0);
    

    $this->setResult(new \Twarch\Result\Text(
      "Imported {$total} Tweets"
    ));

    return true; 
  }

  protected function fetch(Array $params){

    // Request tweets since the last one
    $q = http_build_query($params);
    $url = "http://api.twitter.com/1/statuses/user_timeline.json?{$q}";
    $response = file_get_contents($url);

    if (!$response){
      return false;
    }
    
    $tweets = json_decode($response);
    
    if (sizeOf($tweets) == 0){
      return false;
    }
    
    $addTweet = $this->db->prepare('
      INSERT INTO tweets(id, created, text) values(:id, :created, :text)
    ');

    $count = 0;
    $earliestTweet = null;
    foreach($tweets as $tweet){
      $this->progress("Importing Tweet with ID [{$tweet->id}]");

      if (is_null($earliestTweet)){
        $earliestTweet = $tweet;
      }
      if (strToTime($tweet->created_at) < strToTime($earliestTweet->created_at)){
        $earliestTweet = $tweet;
      }

      $addTweet->execute(array(
        'id'      => $tweet->id,
        'created' => strToTime($tweet->created_at),
        'text'    => str_replace("\n", "", html_entity_decode($tweet->text))
      ));
      $count++;
      
    }
    
    return array(
      'count'         => $count,
      'earliestTweet' => $earliestTweet
    );
  }
}
